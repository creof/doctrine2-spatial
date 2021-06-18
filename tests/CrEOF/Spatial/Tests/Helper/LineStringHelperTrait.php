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
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * LineStringHelperTrait Trait.
 *
 * This helper provides some methods to generates linestring entities.
 * All of these polygonal geometries are defined in test documentation.
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
trait LineStringHelperTrait
{
    /**
     * Create a broken linestring and persist it in database.
     * Line is created with three aligned points: (3 3) (4 15) (5 22).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createAngularLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(3, 3),
            new Point(4, 15),
            new Point(5, 22),
        ]);
    }

    /**
     * Create a linestring A and persist it in database.
     * Line is created with two points: (0 0, 10 10).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringA(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(10, 10),
        ]);
    }

    /**
     * Create a linestring B and persist it in database.
     * Line B crosses lines A and C.
     * Line is created with two points: (0 10, 15 0).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringB(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 10),
            new Point(15, 0),
        ]);
    }

    /**
     * Create a linestring C and persist it in database.
     * Linestring C does not cross linestring A.
     * Linestring C crosses linestring B.
     * Line is created with two points: (2 0, 12 10).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringC(): LineStringEntity
    {
        return $this->createLineString([
            new Point(2, 0),
            new Point(12, 10),
        ]);
    }

    /**
     * Create a linestring X and persist it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringX(): LineStringEntity
    {
        return $this->createLineString([
            new Point(8, 15),
            new Point(4, 8),
        ]);
    }

    /**
     * Create a linestring Y and persist it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringY(): LineStringEntity
    {
        return $this->createLineString([
            new Point(12, 14),
            new Point(3, 4),
        ]);
    }

    /**
     * Create a linestring Z and persist it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringZ(): LineStringEntity
    {
        return $this->createLineString([
            new Point(2, 5),
            new Point(3, 6),
            new Point(12, 8),
            new Point(10, 10),
            new Point(13, 11),
        ]);
    }

    /**
     * Create a node linestring and persist it in database.
     * Line is created with three aligned points: (0 0) (1 0) (0 1) (1 1) (0 0).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createNodeLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(1, 0),
            new Point(0, 1),
            new Point(1, 1),
            new Point(0, 0),
        ]);
    }

    /**
     * Create a ring linestring and persist it in database.
     * Line is created with three aligned points: (0 0) (1 0) (1 1) (0 1) (0 0).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createRingLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(1, 0),
            new Point(1, 1),
            new Point(0, 1),
            new Point(0, 0),
        ]);
    }

    /**
     * Create a straight linestring and persist it in database.
     * Line is created with three aligned points: (0 0) (2 2) (5 5).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createStraightLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(2, 2),
            new Point(5, 5),
        ]);
    }

    /**
     * Create a LineString entity from an array of points.
     *
     * @param Point[] $points the array of points
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    private function createLineString(array $points): LineStringEntity
    {
        $lineStringEntity = new LineStringEntity();
        $lineStringEntity->setLineString(new LineString($points));
        $this->getEntityManager()->persist($lineStringEntity);

        return $lineStringEntity;
    }
}
