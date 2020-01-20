<?php
/**
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
use CrEOF\Spatial\PHP\Types\Geography\GeographyInterface;

/**
 * MySql spatial platform
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class MySql extends AbstractPlatform
{
    /**
     * For Geographic types MySQL follows the WKT specifications and returns (latitude,longitude) while (x,y) / (longitude,latitude) is expected.
     *
     * Using the following option the preferred axis-order can be indicated.
     *
     * @var string
     */
    const AXIS_ORDER_OPTION = 'axis-order=long-lat';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration)
    {
        if ($fieldDeclaration['type']->getSQLType() === GeographyInterface::GEOGRAPHY) {
            return 'GEOMETRY';
        }

        return strtoupper($fieldDeclaration['type']->getSQLType());
    }

    /**
     * @param AbstractSpatialType $type
     * @param string              $sqlExpr
     *
     * @return string
     */
    public function convertToPHPValueSQL(AbstractSpatialType $type, $sqlExpr)
    {
        return $type instanceof GeographyInterface
            ? sprintf('ST_AsBinary(%s, "%s")', $sqlExpr, self::AXIS_ORDER_OPTION)
            : sprintf('ST_AsBinary(%s)', $sqlExpr);
    }

    /**
     * @param AbstractSpatialType $type
     * @param string              $sqlExpr
     *
     * @return string
     */
    public function convertToDatabaseValueSQL(AbstractSpatialType $type, $sqlExpr)
    {
        return $type instanceof GeographyInterface
            ? sprintf('ST_GeomFromText(%s, %d, "%s")', $sqlExpr, $type->getSrid(), self::AXIS_ORDER_OPTION)
            : sprintf('ST_GeomFromText(%s)', $sqlExpr);
    }

}
