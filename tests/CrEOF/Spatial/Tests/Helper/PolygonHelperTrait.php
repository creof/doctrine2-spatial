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
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\Tests\Fixtures\PolygonEntity;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * TestHelperTrait Trait.
 *
 * This helper provides some methods to generates polygons, linestring and point.
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
trait PolygonHelperTrait
{
    /**
     * Create the BIG Polygon and persist it in database.
     * Square (0 0, 10 10).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createBigPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
        ]);
    }

    /**
     * Create a Polygon from an array of linestrings.
     *
     * @param array    $lineStrings the array of linestrings
     * @param int|null $srid        Spatial Reference System Identifier
     *
     * @return PolygonEntity
     * @throws DBALException when credentials fail
     * @throws InvalidValueException when geometries are not valid
     * @throws ORMException when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    private function createPolygon(array $lineStrings, int $srid = null): PolygonEntity
    {
        $polygon = new Polygon($lineStrings);
        if (null !== $srid) {
            $polygon->setSrid($srid);
        }

        $polygonEntity = new PolygonEntity();
        $polygonEntity->setPolygon($polygon);

        $this->getEntityManager()->persist($polygonEntity);

        return $polygonEntity;
    }

    /**
     * Create an eccentric polygon and persist it in database.
     * Square (6 6, 10 10).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createEccentricPolygon(): PolygonEntity
    {
        return $this->createPolygon([new LineString([
            new Point(6, 6),
            new Point(10, 6),
            new Point(10, 10),
            new Point(6, 10),
            new Point(6, 6),
        ])]);
    }

    /**
     * Create the HOLEY Polygon and persist it in database.
     * (Big polygon minus Small Polygon).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createHoleyPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
            new LineString([
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]),
        ]);
    }

    /**
     * Create the Outer Polygon and persist it in database.
     * Square (15 15, 17 17).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createOuterPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(15, 15),
                new Point(17, 15),
                new Point(17, 17),
                new Point(15, 17),
                new Point(15, 15),
            ]),
        ]);
    }

    /**
     * Create the W Polygon and persist it in database.
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createPolygonW(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 20),
                new Point(0, 20),
                new Point(10, 10),
                new Point(0, 0),
            ]),
        ]);
    }

    /**
     * Create the SMALL Polygon and persist it in database.
     * SQUARE (5 5, 7 7).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createSmallPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]),
        ]);
    }

    /**
     * Create the Massachusetts state plane US feet geometry and persist it in database.
     *
     * @param bool $forwardSrid forward SRID for creation
     *
     * @return PolygonEntity
     * @throws DBALException when credentials fail
     * @throws InvalidValueException when geometries are not valid
     * @throws ORMException when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    protected function createMassachusettsState(bool $forwardSrid = true): PolygonEntity
    {
        $srid = null;

        if ($forwardSrid) {
            $srid = 2249;
        }

        return $this->createPolygon([
            new LineString([
                new Point(743238, 2967416),
                new Point(743238, 2967450),
                new Point(743265, 2967450),
                new Point(743265.625, 2967416),
                new Point(743238, 2967416),
            ])
        ], $srid);
    }
}
