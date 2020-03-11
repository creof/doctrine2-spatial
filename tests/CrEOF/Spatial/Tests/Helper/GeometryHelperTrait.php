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
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * GeometryHelperTrait Trait.
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
trait GeometryHelperTrait
{
    /**
     * Create a geometric Point entity from an array of points.
     *
     * @param GeometryInterface $geometry object implementing Geometry interface
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createGeometry(GeometryInterface $geometry): GeometryEntity
    {
        $entity = new GeometryEntity();
        $entity->setGeometry($geometry);
        $this->getEntityManager()->persist($entity);
        return $entity;
    }


    /**
     * Create a geometric point at origin.
     *
     * @param int|null $srid Spatial Reference System Identifier
     *
     * @throws DBALException when credentials fail
     * @throws InvalidValueException when point is an invalid geometry
     * @throws ORMException when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    protected function createPointO(int $srid = null): GeometryEntity
    {
        $point = new Point([0, 0]);
        if (null !== $srid) {
            $point->setSrid($srid);
        }

        return $this->createGeometry($point);
    }

    /**
     * Create a geometric straight linestring.
     *
     * @throws InvalidValueException        when linestring is an invalid geometry
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createStraightLineString(): GeometryEntity
    {
        $straightLineString = new LineString([
            [1, 1],
            [2, 2],
            [5, 5],
        ]);
        return $this->createGeometry($straightLineString);
    }
}
