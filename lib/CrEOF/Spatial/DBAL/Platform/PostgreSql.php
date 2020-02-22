<?php
/**
 * Copyright (C) 2020 Alexandre Tranchant
 * Copyright (C) 2015 Derek J. Lambert
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

namespace CrEOF\Spatial\DBAL\Platform;

use CrEOF\Spatial\DBAL\Types\AbstractSpatialType;
use CrEOF\Spatial\DBAL\Types\GeographyType;
use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;

/**
 * PostgreSql spatial platform.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class PostgreSql extends AbstractPlatform
{
    public const DEFAULT_SRID = 4326;

    /**
     * Convert Binary to php value.
     *
     * @param AbstractSpatialType $type    Spatial type
     * @param string              $sqlExpr Sql expression
     *
     * @return GeometryInterface
     *
     * @throws InvalidValueException when SQL expression is not a resource
     */
    public function convertBinaryToPhpValue(AbstractSpatialType $type, $sqlExpr)
    {
        if (!is_resource($sqlExpr)) {
            throw new InvalidValueException(sprintf('Invalid resource value "%s"', $sqlExpr));
        }

        $sqlExpr = stream_get_contents($sqlExpr);

        return parent::convertBinaryToPhpValue($type, $sqlExpr);
    }

    /**
     * Convert to database value.
     *
     * @param AbstractSpatialType $type  The spatial type
     * @param GeometryInterface   $value The geometry interface
     *
     * @return string
     */
    public function convertToDatabaseValue(AbstractSpatialType $type, GeometryInterface $value)
    {
        $sridSQL = null;

        if ($type instanceof GeographyType && null === $value->getSrid()) {
            $value->setSrid(self::DEFAULT_SRID);
        }

        if (null !== ($srid = $value->getSrid()) || $type instanceof GeographyType) {
            $sridSQL = sprintf('SRID=%d;', $srid);
        }

        return sprintf('%s%s', $sridSQL, parent::convertToDatabaseValue($type, $value));
    }

    /**
     * Convert to database value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     *
     * @return string
     */
    public function convertToDatabaseValueSql(AbstractSpatialType $type, $sqlExpr)
    {
        if ($type instanceof GeographyType) {
            return sprintf('ST_GeographyFromText(%s)', $sqlExpr);
        }

        return sprintf('ST_GeomFromEWKT(%s)', $sqlExpr);
    }

    /**
     * Convert to php value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     *
     * @return string
     */
    public function convertToPhpValueSql(AbstractSpatialType $type, $sqlExpr)
    {
        if ($type instanceof GeographyType) {
            return sprintf('ST_AsEWKT(%s)', $sqlExpr);
        }

        return sprintf('ST_AsEWKB(%s)', $sqlExpr);
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration array SHALL contains 'type' as key
     *
     * @return string
     */
    public function getSqlDeclaration(array $fieldDeclaration)
    {
        $typeFamily = $fieldDeclaration['type']->getTypeFamily();
        $sqlType = $fieldDeclaration['type']->getSQLType();

        if ($typeFamily === $sqlType) {
            return $sqlType;
        }

        if (isset($fieldDeclaration['srid'])) {
            return sprintf('%s(%s,%d)', $typeFamily, $sqlType, $fieldDeclaration['srid']);
        }

        return sprintf('%s(%s)', $typeFamily, $sqlType);
    }
}
