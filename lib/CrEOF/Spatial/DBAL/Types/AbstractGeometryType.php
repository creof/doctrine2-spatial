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

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\DBAL\Types\Platforms\PlatformInterface;
use CrEOF\Spatial\PHP\Types\AbstractGeometry;
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
    abstract public function getBaseClass();

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
        $spatialPlatform = $this->getSpatialPlatform($platform);

        return $spatialPlatform->convertToDatabaseValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        $spatialPlatform = $this->getSpatialPlatform($platform);

        return $spatialPlatform->convertToPHPValueSQL($sqlExpr);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        $spatialPlatform = $this->getSpatialPlatform($platform);

        return $spatialPlatform->convertToDatabaseValueSQL($sqlExpr);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $spatialPlatform = $this->getSpatialPlatform($platform);

        return $spatialPlatform->convertToPHPValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return AbstractGeometry::GEOMETRY;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return PlatformInterface
     * @throws UnsupportedPlatformException
     */
    protected function getSpatialPlatform(AbstractPlatform $platform)
    {
        $spatialPlatformClass = sprintf('CrEOF\Spatial\DBAL\Types\%s\Platforms\%s', $this->getBaseClass(), $platform->getName());

        if ( ! class_exists($spatialPlatformClass)) {
            throw UnsupportedPlatformException::unsupportedPlatform($platform->getName());
        }

        return new $spatialPlatformClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $spatialPlatform = $this->getSpatialPlatform($platform);

        return $spatialPlatform->getSQLDeclaration($fieldDeclaration);
    }
}
