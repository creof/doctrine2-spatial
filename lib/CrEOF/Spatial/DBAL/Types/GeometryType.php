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
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\DBAL\Types\WkbValueParser;
use CrEOF\Spatial\DBAL\Types\WktValueParser;
use CrEOF\Spatial\PHP\Types\AbstractGeometry;
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
    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return AbstractGeometry::GEOMETRY;
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
                if ($this->getName() == AbstractGeometry::GEOMETRY) {
                    return 'geometry';
                }

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
     * @return AbstractGeometry|null
     * @throws UnsupportedPlatformException
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (gettype($value) == 'string' && ord($value) > 31) {
            return $this->convertStringToPHPValue($value, $platform);
        }

        return $this->convertBinaryToPHPValue($value, $platform);
    }

    /**
     * @param AbstractGeometry $value
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof AbstractGeometry) {
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
     * @param string           $sqlExpr
     * @param AbstractPlatform $platform
     *
     * @return AbstractGeometry
     * @throws UnsupportedPlatformException
     */
    protected function convertStringToPHPValue($sqlExpr, AbstractPlatform $platform)
    {
        switch ($platform->getName()) {
            case 'mysql':
                // no break
            case 'postgresql':
                return $this->convertWktValue($sqlExpr);
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
     * @return AbstractGeometry
     * @throws InvalidValueException
     * @throws UnsupportedPlatformException
     */
    protected function convertBinaryToPHPValue($sqlExpr, AbstractPlatform $platform)
    {
        switch ($platform->getName()) {
            case 'mysql':
                break;
            case 'postgresql':
                if ( ! is_resource($sqlExpr)) {
                    throw InvalidValueException::invalidType('resource', $sqlExpr);
                }

                $sqlExpr = stream_get_contents($sqlExpr);
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
                break;
        }

        return $this->convertWkbValue($sqlExpr);
    }

    /**
     * @param string $value
     *
     * @return AbstractGeometry
     */
    protected function convertWktValue($value)
    {
        $parser = new WktValueParser();

        return $parser->parse($value);
    }

    /**
     * @param string $value
     *
     * @return AbstractGeometry
     */
    protected function convertWkbValue($value)
    {
        $parser = new WkbValueParser();

        return $parser->parse($value);
    }
}
