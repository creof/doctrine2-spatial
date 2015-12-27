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

namespace CrEOF\Spatial\DBAL\Types;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\DBAL\Platform\PlatformInterface;
use CrEOF\Spatial\PHP\Types\Geography\GeographyInterface;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Abstract Doctrine GEOMETRY type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractSpatialType extends Type
{
    const PLATFORM_MYSQL      = 'MySql';
    const PLATFORM_POSTGRESQL = 'PostgreSql';

    /**
     * @return string
     */
    public function getTypeFamily()
    {
        return $this instanceof GeographyType ? GeographyInterface::GEOGRAPHY : GeometryInterface::GEOMETRY;
    }

    /**
     * Gets the SQL name of this type.
     *
     * @return string
     */
    public function getSQLType()
    {
        $class = get_class($this);
        $start = strrpos($class, '\\') + 1;
        $len   = strlen($class) - $start - 4;

        return substr($class, strrpos($class, '\\') + 1, $len);
    }

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
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
     * @throws InvalidValueException
     * @throws UnsupportedPlatformException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if (! ($value instanceof GeometryInterface)) {
            throw new InvalidValueException('Geometry column values must implement GeometryInterface');
        }

        return $this->getSpatialPlatform($platform)->convertToDatabaseValue($this, $value);
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
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @param string           $sqlExpr
     * @param AbstractPlatform $platform
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
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (ctype_alpha($value[0])) {
            return $this->getSpatialPlatform($platform)->convertStringToPHPValue($this, $value);
        }

        return $this->getSpatialPlatform($platform)->convertBinaryToPHPValue($this, $value);
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
     * @param array            $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $this->getSpatialPlatform($platform)->getSQLDeclaration($fieldDeclaration);
    }

    /**
     * Get an array of database types that map to this Doctrine type.
     *
     * @param AbstractPlatform $platform
     *
     * @return array
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return $this->getSpatialPlatform($platform)->getMappedDatabaseTypes($this);
    }

    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't take them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     *
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        // TODO onSchemaColumnDefinition event listener?
        return true;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return PlatformInterface
     * @throws UnsupportedPlatformException
     */
    private function getSpatialPlatform(AbstractPlatform $platform)
    {
        $const = sprintf('self::PLATFORM_%s', strtoupper($platform->getName()));

        if (! defined($const)) {
            throw new UnsupportedPlatformException(sprintf('DBAL platform "%s" is not currently supported.', $platform->getName()));
        }

        $class = sprintf('CrEOF\Spatial\DBAL\Platform\%s', constant($const));

        return new $class;
    }
}
