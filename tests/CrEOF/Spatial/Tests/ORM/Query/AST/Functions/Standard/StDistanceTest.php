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
use CrEOF\Spatial\Tests\Helper\PointHelperTrait;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

/**
 * ST_Distance DQL function tests.
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
class StDistanceTest extends OrmTestCase
{
    use PointHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->usesType('geopoint');
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geography
     */
    public function testSelectStDistanceGeographyCartesian()
    {
        $newYork = $this->createNewYorkGeography();
        $losAngeles = $this->createLosAngelesGeography();
        $dallas = $this->createDallasGeography();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT g, ST_Distance(g.geography, PgSql_GeographyFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g'
            // phpcs:enable
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        //TODO: Test should be fixed, distance are differents on Windows and on Linux.
        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertGreaterThan(1309000, $result[0][1]);
        static::assertLessThan(1310000, $result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertGreaterThan(2680000, $result[1][1]);
        static::assertLessThan(2690000, $result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertGreaterThan(1310000, $result[2][1]);
        static::assertLessThan(1320000, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geography
     */
    public function testSelectStDistanceGeographySpheroid()
    {
        $newYork = $this->createNewYorkGeography();
        $losAngeles = $this->createLosAngelesGeography();
        $dallas = $this->createDallasGeography();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        //TODO This test should be moved to a class implementing only PgSQL
        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT g, ST_Distance(g.geography, PgSql_GeographyFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g'
            // phpcs:enable
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertEquals(1309106.31458423, $result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertEquals(2689041.41288843, $result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertEquals(1312731.61417061, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStDistanceGeometryCartesian()
    {
        $newYork = $this->createNewYorkGeometry();
        $losAngeles = $this->createLosAngelesGeometry();
        $dallas = $this->createDallasGeometry();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_Distance(p.point, ST_GeomFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertEquals(15.646934398128, $result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertEquals(30.2188561049899, $result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertEquals(12.6718564262953, $result[2][1]);
    }
}
