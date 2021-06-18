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

namespace CrEOF\Spatial\Tests\Helper;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use CrEOF\Spatial\Tests\Fixtures\GeographyEntity;
use CrEOF\Spatial\Tests\Fixtures\PointEntity;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * PointHelperTrait Trait.
 *
 * This helper provides some methods to generates point entities.
 * All of these points are defined in test documentation.
 *
 * Methods beginning with create will store a geo* entity in database.
 *
 * @see /doc/test.md
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @method EntityManagerInterface getEntityManager Return the entity interface
 */
trait PointHelperTrait
{
    /**
     * Create Dallas geography Point entity and store it in database.
     *
     * @throws InvalidValueException        when geographies are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createDallasGeography(): GeographyEntity
    {
        return $this->createGeography(new GeographyPoint(-96.803889, 32.782778));
    }

    /**
     * Create Dallas geometry Point entity and store it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createDallasGeometry(): PointEntity
    {
        return $this->createGeometry(new GeometryPoint(-96.803889, 32.782778));
    }

    /**
     * Create Los Angeles geography Point entity and store it in database.
     *
     * @throws InvalidValueException        when geographies are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLosAngelesGeography(): GeographyEntity
    {
        return $this->createGeography(new GeographyPoint(-118.2430, 34.0522));
    }

    /**
     * Create Los Angeles geometry Point entity and store it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLosAngelesGeometry(): PointEntity
    {
        return $this->createGeometry(new GeometryPoint(-118.2430, 34.0522));
    }

    /**
     * Create New York geography Point entity and store it in database.
     *
     * @throws InvalidValueException        when geographies are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createNewYorkGeography(): GeographyEntity
    {
        return $this->createGeography(new GeographyPoint(-73.938611, 40.664167));
    }

    /**
     * Create New York geometry Point entity and store it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createNewYorkGeometry(): PointEntity
    {
        return $this->createGeometry(new GeometryPoint(-73.938611, 40.664167));
    }

    /**
     * Create the point A (1, 2).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createPointA(): PointEntity
    {
        return $this->createGeometry(new GeometryPoint(1, 2));
    }

    /**
     * Create the point B (-2, 3).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createPointB(): PointEntity
    {
        return $this->createGeometry(new GeometryPoint(-2, 3));
    }

    /**
     * Create the point E (5, 5).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createPointE(): PointEntity
    {
        return $this->createGeometry(new GeometryPoint(5, 5));
    }

    /**
     * Create the point origin (0, 0).
     *
     * @param bool $setSrid Set the SRID to zero instead of null
     *
     * @throws DBALException                when credentials fail
     * @throws InvalidValueException        when geometries are not valid
     * @throws ORMException                 when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    protected function createPointO($setSrid = false): PointEntity
    {
        $geometryEntity = $this->createGeometry(new GeometryPoint(0, 0));
        if ($setSrid) {
            $geometryEntity->getPoint()->setSrid(0);
        }

        return $geometryEntity;
    }

    /**
     * Create Tours city in Lambert93 (French SRID) as geometry Point entity and store it in database.
     *
     * @param bool $setSrid initialize the SRID to 2154 if true
     *
     * @throws DBALException                when credentials fail
     * @throws InvalidValueException        when geometries are not valid
     * @throws ORMException                 when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    protected function createToursLambert93(bool $setSrid = true): PointEntity
    {
        $pointEntity = $this->createGeometry(new GeometryPoint(525375.21, 6701871.83));
        if ($setSrid) {
            $pointEntity->getPoint()->setSrid(2154);
        }

        return $pointEntity;
    }

    /**
     * Create a geographic Point entity from an array of points.
     *
     * @param GeographyPoint|array $point Point could be an array of X, Y or an instance of Point class
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    private function createGeography(GeographyPoint $point): GeographyEntity
    {
        $pointEntity = new GeographyEntity();
        $pointEntity->setGeography($point);
        $this->getEntityManager()->persist($pointEntity);

        return $pointEntity;
    }

    /**
     * Create a geometric Point entity from an array of points.
     *
     * @param GeometryPoint|array $point Point could be an array of X, Y or an instance of Point class
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    private function createGeometry(GeometryPoint $point): PointEntity
    {
        $pointEntity = new PointEntity();
        $pointEntity->setPoint($point);
        $this->getEntityManager()->persist($pointEntity);

        return $pointEntity;
    }
}
