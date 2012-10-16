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
use CrEOF\Spatial\PHP\Types\AbstractGeometry;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Doctrine GEOGRAPHY type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeographyType extends GeometryType
{
    const GEOGRAPHY   = 'geography';

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::GEOGRAPHY;
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
            case 'postgresql':
                if ($this->getName() == 'geography') {
                    return 'geography';
                }

                return 'geography('. strtoupper($this->getName()) . ')';
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
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        switch ($platform->getName()) {
            case 'postgresql':
                return sprintf('ST_AsEWKT(%s)', $sqlExpr);
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
            case 'postgresql':
                return sprintf('ST_GeographyFromText(%s)', $sqlExpr);
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
            case 'postgresql':
                return $this->convertEwktValue($sqlExpr);
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
                break;
        }
    }

    /**
     * @param string $value
     *
     * @return AbstractGeometry
     */
    protected function convertEwktValue($value)
    {
        $parser = new EwktValueParser();

        return $parser->parse($value);
    }

}
