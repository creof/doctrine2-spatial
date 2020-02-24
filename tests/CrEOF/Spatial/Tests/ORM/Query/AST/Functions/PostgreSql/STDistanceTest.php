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
use CrEOF\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\GeographyEntity;
use CrEOF\Spatial\Tests\Fixtures\PointEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use CrEOF\Spatial\Tests\TestHelperTrait;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * ST_Distance DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class STDistanceTest extends OrmTestCase
{
    use TestHelperTrait;

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
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geography
     */
    public function testSelectStDistanceGeographyCartesian()
    {
        $newYork = new GeographyEntity();
        $newYork->setGeography(new GeographyPoint(-73.938611, 40.664167));
        $this->getEntityManager()->persist($newYork);

        $losAngeles = new GeographyEntity();
        $losAngeles->setGeography(new GeographyPoint(-118.2430, 34.0522));
        $this->getEntityManager()->persist($losAngeles);

        $dallas = new GeographyEntity();
        $dallas->setGeography(new GeographyPoint(-96.803889, 32.782778));
        $this->getEntityManager()->persist($dallas);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g, ST_Distance(g.geography, ST_GeographyFromText(:p1), false) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g'
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($newYork, $result[0][0]);
        $this->assertEquals(1305895.94823465, $result[0][1]);
        $this->assertEquals($losAngeles, $result[1][0]);
        $this->assertEquals(2684082.08249337, $result[1][1]);
        $this->assertEquals($dallas, $result[2][0]);
        $this->assertEquals(1313754.60684762, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
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
    public function testSelectStDistanceGeographySpheroid()
    {
        $newYork = new GeographyEntity();
        $newYork->setGeography(new GeographyPoint(-73.938611, 40.664167));
        $this->getEntityManager()->persist($newYork);

        $losAngeles = new GeographyEntity();
        $losAngeles->setGeography(new GeographyPoint(-118.2430, 34.0522));
        $this->getEntityManager()->persist($losAngeles);

        $dallas = new GeographyEntity();
        $dallas->setGeography(new GeographyPoint(-96.803889, 32.782778));
        $this->getEntityManager()->persist($dallas);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g, ST_Distance(g.geography, ST_GeographyFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g'
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($newYork, $result[0][0]);
        $this->assertEquals(1309106.31458423, $result[0][1]);
        $this->assertEquals($losAngeles, $result[1][0]);
        $this->assertEquals(2689041.41288843, $result[1][1]);
        $this->assertEquals($dallas, $result[2][0]);
        $this->assertEquals(1312731.61417061, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test.
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
    public function testSelectStDistanceGeometryCartesian()
    {
        $newYork = new PointEntity();
        $newYork->setPoint(new Point(-73.938611, 40.664167));
        $this->getEntityManager()->persist($newYork);

        $losAngeles = new PointEntity();
        $losAngeles->setPoint(new Point(-118.2430, 34.0522));
        $this->getEntityManager()->persist($losAngeles);

        $dallas = new PointEntity();
        $dallas->setPoint(new Point(-96.803889, 32.782778));
        $this->getEntityManager()->persist($dallas);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_Distance(p.point, ST_GeomFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($newYork, $result[0][0]);
        $this->assertEquals(15.646934398128, $result[0][1]);
        $this->assertEquals($losAngeles, $result[1][0]);
        $this->assertEquals(30.2188561049899, $result[1][1]);
        $this->assertEquals($dallas, $result[2][0]);
        $this->assertEquals(12.6718564262953, $result[2][1]);
    }
}
