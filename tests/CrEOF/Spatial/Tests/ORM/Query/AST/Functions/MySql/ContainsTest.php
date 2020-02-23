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

namespace CrEOF\Spatial\Tests\ORM\Query\AST\Functions\MySql;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Contains DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class ContainsTest extends OrmTestCase
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
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->usesType('point');
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the predicate.
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
    public function testContainsWhereParameter()
    {
        $bigPolygon = $this->createPolygon([$this->createEnvelopingLineString()]);
        $smallPolygon = $this->createPolygon([$this->createInternalLineString()]);
        $holeyPolygon = $this->createPolygon([$this->createEnvelopingLineString(), $this->createInternalLineString()]);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p WHERE Contains(p.polygon, GeomFromText(:p)) = 1'
        );

        $query->setParameter('p', 'POINT(6 6)', 'string');
        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($bigPolygon, $result[0]);
        $this->assertEquals($smallPolygon, $result[1]);
        $this->assertEquals($holeyPolygon, $result[2]);
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p WHERE Contains(p.polygon, GeomFromText(:p)) = 1'
        );
        $query->setParameter('p', 'POINT(2 2)', 'string');
        $result = $query->getResult();

        $this->assertCount(2, $result);
        $this->assertEquals($bigPolygon, $result[0]);
        $this->assertEquals($holeyPolygon, $result[1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
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
    public function testSelectContains()
    {
        $bigPolygon = $this->createPolygon([$this->createEnvelopingLineString()]);
        $smallPolygon = $this->createPolygon([$this->createInternalLineString()]);
        $holeyPolygon = $this->createPolygon([$this->createEnvelopingLineString(), $this->createInternalLineString()]);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p, Contains(p.polygon, GeomFromText(:p1)), Contains(p.polygon, GeomFromText(:p2)) FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p'
            // phpcs:enable
        );

        $query->setParameter('p1', 'POINT(2 2)', 'string');
        $query->setParameter('p2', 'POINT(6 6)', 'string');

        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($bigPolygon, $result[0][0]);
        $this->assertEquals(1, $result[0][1]);
        $this->assertEquals(1, $result[0][2]);
        $this->assertEquals($smallPolygon, $result[1][0]);
        $this->assertEquals(0, $result[1][1]);
        $this->assertEquals(1, $result[1][2]);
        $this->assertEquals($holeyPolygon, $result[2][0]);
        $this->assertEquals(1, $result[2][1]);
        $this->assertEquals(1, $result[2][2]);
    }
}
