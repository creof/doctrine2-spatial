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

namespace CrEOF\Spatial\DBAL\Types;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\AbstractGeometry;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

/**
 * Parse spatial WKB values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class WkbValueParser
{
    /**
     * @var int
     */
    protected $byteOrder;

    /**
     * @param string $value
     *
     * @return AbstractGeometry
     * @throws InvalidValueException
     */
    public function parse($value)
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

        $method = 'convertTo' . $this->getTypeName($data['wkbType']);

        return $this->$method($data['value']);
    }

    /**
     * @param int $wkbType
     *
     * @return string
     * @throws InvalidValueException
     */
    protected function getTypeName($wkbType)
    {
        switch ($wkbType) {
            case (0):
                return AbstractGeometry::GEOMETRY;
                break;
            case (1):
                return AbstractGeometry::POINT;
                break;
            case (2):
                return AbstractGeometry::LINESTRING;
                break;
            case (3):
                return AbstractGeometry::POLYGON;
                break;
            default:
                throw InvalidValueException::unsupportedWkbType($wkbType);
                break;
        }
    }

    /**
     * @param string $value
     *
     * @return Point
     */
    protected function convertToPoint($value)
    {
        $data = $this->unpackWkbPoint($value);

        return $this->createPoint($data['x'], $data['y']);
    }

    /**
     * @param string $value
     *
     * @return LineString
     */
    protected function convertToLineString($value)
    {
        $data = $this->unpackWkbLineString($value);

        return $this->createLineString($data['points']);
    }

    /**
     * @param string $value
     *
     * @return Polygon
     */
    protected function convertToPolygon($value)
    {
        $data = $this->unpackWkbPolygon($value);

        return $this->createPolygon($data['rings']);
    }

    /**
     * @param string $value
     *
     * @return array
     * @throws InvalidValueException
     */
    protected function unpackWkbPoint($value)
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
    protected function unpackWkbLineString($value)
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
            $points[] = $this->createPoint($data['x'], $data['y']);
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
    protected function unpackWkbPolygon($value)
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
            $rings[] = $this->createLineString($data['points']);
        }

        return array(
            'value'  => $data['value'],
            'rings' => $rings
        );
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Point
     */
    protected function createPoint($latitude, $longitude)
    {
        return new Point($latitude, $longitude);
    }

    /**
     * @param Point[] $points
     *
     * @return LineString
     */
    protected function createLineString(array $points)
    {
        return new LineString($points);
    }

    /**
     * @param LineString[] $rings
     *
     * @return Polygon
     */
    protected function createPolygon(array $rings)
    {
        return new Polygon($rings);
    }
}
