<?php
/**
 * Copyright (C) 2012 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\DBAL\Types\Spatial;

use CrEOF\Exception\InvalidValueException;
use CrEOF\Exception\UnsupportedPlatformException;
use CrEOF\PHP\Types\Spatial\Geometry;
use CrEOF\PHP\Types\Spatial\Geometry\LineString;
use CrEOF\PHP\Types\Spatial\Geometry\Point;
use CrEOF\PHP\Types\Spatial\Geometry\Polygon;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Doctrine GEOMETRY type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeometryType extends Type
{
    const GEOMETRY    = 'geometry';
    const POINT       = 'point';
    const LINESTRING  = 'linestring';
    const POLYGON     = 'polygon';

    protected $byteOrder;

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::GEOMETRY;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array            $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform         The currently used database platform.
     *
     * @return string
     * @throws UnsupportedPlatformException
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        switch ($platform->getName()) {
            case 'mysql':
                return strtoupper($this->getName());
                break;
            case 'postgresql':
                return 'geometry('. strtoupper($this->getName()) . ')';
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
                break;
        }
    }

    /**
     * @param string           $value
     * @param AbstractPlatform $platform
     *
     * @return Geometry|null
     * @throws UnsupportedPlatformException
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        switch ($platform->getName()) {
            case 'mysql':
                break;
            case 'postgresql':
                if (!is_resource($value)) {
                    throw \Doctrine\DBAL\Types\ConversionException::conversionFailed($value, self::GEOMETRY);
                }

                $value = stream_get_contents($value);
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
                break;
        }

        return $this->convertWkbValue($value);
    }

    /**
     * @param Point            $value
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Geometry) {
            return (string) $value;
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * @param string           $sqlExpr
     * @param AbstractPlatform $platform
     *
     * @return string
     * @throws UnsupportedPlatformException
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        switch ($platform->getName()) {
            case 'mysql':
                return sprintf('AsBinary(%s)', $sqlExpr);
                break;
            case 'postgresql':
                return sprintf('ST_AsBinary(%s)', $sqlExpr);
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
                break;
        }
    }

    /**
     * @param string           $sqlExpr
     * @param AbstractPlatform $platform
     *
     * @return string
     * @throws UnsupportedPlatformException
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        switch ($platform->getName()) {
            case 'mysql':
                return sprintf('GeomFromText(%s)', $sqlExpr);
                break;
            case 'postgresql':
                return sprintf('ST_GeomFromText(%s)', $sqlExpr);
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
                break;
        }
    }

    /**
     * @param int $wkbType
     *
     * @return null|string
     */
    private function getTypeName($wkbType)
    {
        switch ($wkbType) {
            case (0):
                return self::GEOMETRY;
                break;
            case (1):
                return self::POINT;
                break;
            case (2):
                return self::LINESTRING;
                break;
            case (3):
                return self::POLYGON;
                break;
            default:
                return null;
        }
    }

    /**
     * @param string $value
     *
     * @return Geometry
     * @throws InvalidValueException
     */
    private function convertWkbValue($value)
    {
        $data            = unpack('CbyteOrder/A*value', $value);
        $this->byteOrder = $data['byteOrder'];

        switch ($this->byteOrder) {
            case 0:
                $data = unpack('NwkbType/A*value', $data['value']);
                break;
            case 1:
                $data = unpack('VwkbType/A*value', $data['value']);
                break;
            default:
                throw InvalidValueException::invalidByteOrder($this->byteOrder);
                break;
        }

        $method = 'convertWkbTo' . $this->getTypeName($data['wkbType']);

        return $this->$method($data['value']);
    }

    /**
     * @param string $value
     *
     * @return Point
     */
    private function convertWkbToPoint($value)
    {
        $data = $this->unpackWkbPoint($value);

        return new Point($data['x'], $data['y']);
    }

    /**
     * @param string $value
     *
     * @return LineString
     */
    private function convertWkbToLineString($value)
    {
        $data = $this->unpackWkbLineString($value);

        return new LineString($data['points']);
    }

    /**
     * @param string $value
     *
     * @return Polygon
     */
    private function convertWkbToPolygon($value)
    {
        $data = $this->unpackWkbPolygon($value);

        return new Polygon($data['rings']);
    }

    /**
     * @param string $value
     *
     * @return array
     * @throws InvalidValueException
     */
    private function unpackWkbPoint($value)
    {
        switch ($this->byteOrder) {
            case 0:
                $data  = unpack('dx/dy/A*value', $value);
                $fixed = unpack('dy/dx', strrev(pack('dd', $data['x'], $data['y'])));
                $fixed['value'] = $data['value'];

                return $fixed;
                break;
            case 1:
                return unpack('dx/dy/A*value', $value);
                break;
            default:
                throw InvalidValueException::invalidByteOrder($this->byteOrder);
                break;
        }
    }

    /**
     * @param string $value
     *
     * @return array
     * @throws InvalidValueException
     */
    private function unpackWkbLineString($value)
    {
        switch ($this->byteOrder) {
            case 0:
                $data = unpack('NnumPoints/A*value', $value);
                break;
            case 1:
                $data = unpack('VnumPoints/A*value', $value);
                break;
            default:
                throw InvalidValueException::invalidByteOrder($this->byteOrder);
                break;
        }

        $numPoints = $data['numPoints'];
        $points    = array();

        for ($j = 0; $j < $numPoints; $j++) {
            $data     = $this->unpackWkbPoint($data['value']);
            $points[] = new Point($data['x'], $data['y']);
        }

        return array(
            'value'  => $data['value'],
            'points' => $points
        );
    }

    /**
     * @param string $value
     *
     * @return array
     * @throws InvalidValueException
     */
    private function unpackWkbPolygon($value)
    {
        switch ($this->byteOrder) {
            case 0:
                $data = unpack('NnumRings/A*value', $value);
                break;
            case 1:
                $data = unpack('VnumRings/A*value', $value);
                break;
            default:
                throw InvalidValueException::invalidByteOrder($this->byteOrder);
                break;
        }

        $numRings = $data['numRings'];
        $rings   = array();

        for ($i = 0; $i < $numRings; $i++) {
            $data    = $this->unpackWkbLineString($data['value']);
            $rings[] = new LineString($data['points']);
        }

        return array(
            'value'  => $data['value'],
            'rings' => $rings
        );
    }
}
