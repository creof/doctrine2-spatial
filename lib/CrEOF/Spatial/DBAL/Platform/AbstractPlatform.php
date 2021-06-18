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

use CrEOF\Geo\WKB\Parser as BinaryParser;
use CrEOF\Geo\WKT\Parser as StringParser;
use CrEOF\Spatial\DBAL\Types\AbstractSpatialType;
use CrEOF\Spatial\DBAL\Types\GeographyType;
use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;

/**
 * Abstract spatial platform.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 */
abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * Convert binary data to a php value.
     *
     * @param AbstractSpatialType $type    The abstract spatial type
     * @param string              $sqlExpr the SQL expression
     *
     * @throws InvalidValueException when the provided type is not supported
     *
     * @return GeometryInterface
     */
    public function convertBinaryToPhpValue(AbstractSpatialType $type, $sqlExpr)
    {
        $parser = new BinaryParser($sqlExpr);

        return $this->newObjectFromValue($type, $parser->parse());
    }

    /**
     * Convert string data to a php value.
     *
     * @param AbstractSpatialType $type    The abstract spatial type
     * @param string              $sqlExpr the SQL expression
     *
     * @throws InvalidValueException when the provided type is not supported
     *
     * @return GeometryInterface
     */
    public function convertStringToPhpValue(AbstractSpatialType $type, $sqlExpr)
    {
        $parser = new StringParser($sqlExpr);

        return $this->newObjectFromValue($type, $parser->parse());
    }

    // phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed

    /**
     * Convert binary data to a php value.
     *
     * @param AbstractSpatialType $type  The spatial type
     * @param GeometryInterface   $value The geometry object
     *
     * @return string
     */
    public function convertToDatabaseValue(AbstractSpatialType $type, GeometryInterface $value)
    {
        //the unused variable $type is used by overriding method
        return sprintf('%s(%s)', mb_strtoupper($value->getType()), $value);
    }

    // phpcs:enable

    /**
     * Get an array of database types that map to this Doctrine type.
     *
     * @param AbstractSpatialType $type the spatial type
     *
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractSpatialType $type)
    {
        $sqlType = mb_strtolower($type->getSQLType());

        if ($type instanceof GeographyType && 'geography' !== $sqlType) {
            $sqlType = sprintf('geography(%s)', $sqlType);
        }

        return [$sqlType];
    }

    /**
     * Create spatial object from parsed value.
     *
     * @param AbstractSpatialType $type  The type spatial type
     * @param array               $value The value of the spatial object
     *
     * @throws InvalidValueException when the provided type is not supported
     *
     * @return GeometryInterface
     */
    private function newObjectFromValue(AbstractSpatialType $type, $value)
    {
        $typeFamily = $type->getTypeFamily();
        $typeName = mb_strtoupper($value['type']);

        $constName = sprintf('%s::%s', GeometryInterface::class, $typeName);

        if (!defined($constName)) {
            throw new InvalidValueException(sprintf('Unsupported %s type "%s".', $typeFamily, $typeName));
        }

        $class = sprintf('CrEOF\Spatial\PHP\Types\%s\%s', $typeFamily, constant($constName));

        return new $class($value['value'], $value['srid']);
    }
}
