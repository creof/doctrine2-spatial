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
use CrEOF\Spatial\DBAL\Types\Platforms\PlatformInterface;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Abstract Doctrine GEOMETRY type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractGeometryType extends Type
{
    const PLATFORM_MYSQL = 'MySql';
    const PLATFORM_POSTGRESQL = 'PostgreSql';

    /**
     * @return string
     */
    abstract public function getTypeFamily();

    /**
     * Gets the SQL name of this type.
     *
     * @return string
     */
    abstract public function getSQLType();

    /**
     * {@inheritdoc}
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ( ! ($value instanceof GeometryInterface)) {
            throw InvalidValueException::invalidValueNoGeometryInterface();
        }

        return $this->getSpatialPlatform($platform)->convertToDatabaseValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return $this->getSpatialPlatform($platform)->convertToPHPValueSQL($sqlExpr);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return $this->getSpatialPlatform($platform)->convertToDatabaseValueSQL($sqlExpr);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (ctype_alpha($value[0])) {
            return $this->getSpatialPlatform($platform)->convertStringToPHPValue($value);
        }

        return $this->getSpatialPlatform($platform)->convertBinaryToPHPValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return array_search(get_class($this), $this->getTypesMap());
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $this->getSpatialPlatform($platform)->getSQLDeclaration($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return array(strtolower($this->getSQLType()));
    }

    /**
     * {@inheritdoc}
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
        if ( ! class_exists($spatialPlatformClass = $this->getSpatialPlatformClass($platform))) {
            throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
        }

        return new $spatialPlatformClass;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return string
     * @throws UnsupportedPlatformException
     */
    private function getPlatformName(AbstractPlatform $platform)
    {
        $name = __CLASS__ . '::PLATFORM_' . strtoupper($platform->getName());

        if ( ! defined($name)) {
            throw UnsupportedPlatformException::unsupportedPlatform($name);
        }

        return constant($name);
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    private function getSpatialPlatformClass(AbstractPlatform $platform)
    {
        return sprintf('CrEOF\Spatial\DBAL\Types\%s\Platforms\%s', $this->getTypeFamily(), $this->getPlatformName($platform));
    }
}
