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

namespace CrEOF\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geography\LineString as GeographyLineString;
use CrEOF\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use CrEOF\Spatial\PHP\Types\Geography\Polygon as GeographyPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\LineString as GeometryLineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon as GeometryPolygon;
use CrEOF\Spatial\Tests\Fixtures\GeographyEntity;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * SP_Summary DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class SpSummaryTest extends OrmTestCase
{
    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select with a geography.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geography
     */
    public function testSelectStSummaryGeography()
    {
        $point = new GeographyEntity();
        $point->setGeography(new GeographyPoint(5, 5));
        $this->getEntityManager()->persist($point);

        $linestring = new GeographyEntity();
        $linestring->setGeography(new GeographyLineString([
            [1, 1],
            [2, 2],
            [3, 3],
        ]));
        $this->getEntityManager()->persist($linestring);

        $polygon = new GeographyEntity();
        $polygon->setGeography(new GeographyPolygon([[
            [0, 0],
            [10, 0],
            [10, 10],
            [0, 10],
            [0, 0],
        ]]));
        $this->getEntityManager()->persist($polygon);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g, PgSql_Summary(g.geography) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g'
        );
        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($point, $result[0][0]);
        static::assertRegExp('/^Point\[.*G.*\]/', $result[0][1]);
        static::assertEquals($linestring, $result[1][0]);
        static::assertRegExp('/^LineString\[.*G.*\]/', $result[1][1]);
        static::assertEquals($polygon, $result[2][0]);
        static::assertRegExp('/^Polygon\[.*G.*\]/', $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the select with a geometry.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSummaryGeometry()
    {
        $point = new GeometryEntity();
        $point->setGeometry(new GeometryPoint(5, 5));
        $this->getEntityManager()->persist($point);

        $linestring = new GeometryEntity();
        $linestring->setGeometry(new GeometryLineString([
            [1, 1],
            [2, 2],
            [3, 3],
        ]));
        $this->getEntityManager()->persist($linestring);

        $polygon = new GeometryEntity();
        $polygon->setGeometry(new GeometryPolygon([[
            [0, 0],
            [10, 0],
            [10, 10],
            [0, 10],
            [0, 0],
        ]]));
        $this->getEntityManager()->persist($polygon);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g, PgSql_Summary(g.geometry) FROM CrEOF\Spatial\Tests\Fixtures\GeometryEntity g'
        );
        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($point, $result[0][0]);
        static::assertRegExp('/^Point\[[^G]*\]/', $result[0][1]);
        static::assertEquals($linestring, $result[1][0]);
        static::assertRegExp('/^LineString\[[^G]*\]/', $result[1][1]);
        static::assertEquals($polygon, $result[2][0]);
        static::assertRegExp('/^Polygon\[[^G]*\]/', $result[2][1]);
    }
}
