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

namespace CrEOF\Spatial\DBAL\Types;

use CrEOF\Spatial\DBAL\Platform\PlatformInterface;
use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geography\GeographyInterface;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Abstract Doctrine GEOMETRY type.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractSpatialType extends Type
{
    public const PLATFORM_MYSQL = 'MySql';
    public const PLATFORM_POSTGRESQL = 'PostgreSql';

    /**
     * @return bool
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * Converts a value from its PHP representation to its database representation of this type.
     *
     * @param mixed $value
     *
     * @throws InvalidValueException
     * @throws UnsupportedPlatformException
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        if (!($value instanceof GeometryInterface)) {
            throw new InvalidValueException('Geometry column values must implement GeometryInterface');
        }

        return $this->getSpatialPlatform($platform)->convertToDatabaseValue($this, $value);
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @param string $sqlExpr
     *
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return $this->getSpatialPlatform($platform)->convertToDatabaseValueSQL($this, $sqlExpr);
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!is_resource($value) && ctype_alpha($value[0])) {
            return $this->getSpatialPlatform($platform)->convertStringToPHPValue($this, $value);
        }

        return $this->getSpatialPlatform($platform)->convertBinaryToPHPValue($this, $value);
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a PHP value.
     *
     * @param string           $sqlExpr
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return $this->getSpatialPlatform($platform)->convertToPHPValueSQL($this, $sqlExpr);
    }

    /**
     * Get an array of database types that map to this Doctrine type.
     *
     * @return array
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return $this->getSpatialPlatform($platform)->getMappedDatabaseTypes($this);
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return array_search(get_class($this), self::getTypesMap(), true);
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $this->getSpatialPlatform($platform)->getSQLDeclaration($fieldDeclaration);
    }

    /**
     * Gets the SQL name of this type.
     *
     * @return string
     */
    public function getSQLType()
    {
        $class = get_class($this);
        $start = mb_strrpos($class, '\\') + 1;
        $len = mb_strlen($class) - $start - 4;

        return mb_substr($class, mb_strrpos($class, '\\') + 1, $len);
    }

    /**
     * @return string
     */
    public function getTypeFamily()
    {
        return $this instanceof GeographyType ? GeographyInterface::GEOGRAPHY : GeometryInterface::GEOMETRY;
    }

    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't take them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        // TODO onSchemaColumnDefinition event listener?
        return true;
    }

    /**
     * @throws UnsupportedPlatformException
     *
     * @return PlatformInterface
     */
    private function getSpatialPlatform(AbstractPlatform $platform)
    {
        $const = sprintf('self::PLATFORM_%s', mb_strtoupper($platform->getName()));

        if (!defined($const)) {
            throw new UnsupportedPlatformException(sprintf('DBAL platform "%s" is not currently supported.', $platform->getName()));
        }

        $class = sprintf('CrEOF\Spatial\DBAL\Platform\%s', constant($const));

        return new $class();
    }
}
