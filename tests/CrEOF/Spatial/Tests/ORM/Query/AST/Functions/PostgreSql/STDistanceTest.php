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

namespace CrEOF\Spatial\Tests\ORM\Functions\PostgreSql;

use CrEOF\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\PointEntity;
use CrEOF\Spatial\Tests\Fixtures\GeographyEntity;
use CrEOF\Spatial\Tests\OrmTest;
use Doctrine\ORM\Query;

/**
 * ST_Distance DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group postgresql
 * @group dql
 */
class STDistanceTest extends OrmTest
{
    protected function setUp()
    {
        $this->useEntity('point');
        $this->useEntity('geography');
        $this->useType('geopoint');
        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSelectSTDistanceGeometryCartesian()
    {
        $newYork   = new Point(-73.938611, 40.664167);
        $losAngles = new Point(-118.2430, 34.0522);
        $dallas    = new Point(-96.803889, 32.782778);
        $madison   = new Point(-89.4, 43.066667);

        $entity1 = new PointEntity();

        $entity1->setPoint($newYork);
        $this->_em->persist($entity1);

        $entity2 = new PointEntity();

        $entity2->setPoint($losAngles);
        $this->_em->persist($entity2);

        $entity3 = new PointEntity();

        $entity3->setPoint($dallas);
        $this->_em->persist($entity3);
        $this->_em->flush();
        $this->_em->clear();

        $query = $this->_em->createQuery('SELECT p, ST_Distance(p.point, ST_GeomFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');

        $query->setParameter('p1', $madison, 'point');

        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($entity1, $result[0][0]);
        $this->assertEquals(15.646934398128, $result[0][1]);
        $this->assertEquals($entity2, $result[1][0]);
        $this->assertEquals(30.2188561049899, $result[1][1]);
        $this->assertEquals($entity3, $result[2][0]);
        $this->assertEquals(12.6718564262953, $result[2][1]);
    }

    /**
     * @group geography
     */
    public function testSelectSTDistanceGeographySpheroid()
    {
        $newYork   = new GeographyPoint(-73.938611, 40.664167);
        $losAngles = new GeographyPoint(-118.2430, 34.0522);
        $dallas    = new GeographyPoint(-96.803889, 32.782778);
        $madison   = new GeographyPoint(-89.4, 43.066667);

        $entity1 = new GeographyEntity();

        $entity1->setGeography($newYork);
        $this->_em->persist($entity1);

        $entity2 = new GeographyEntity();

        $entity2->setGeography($losAngles);
        $this->_em->persist($entity2);

        $entity3 = new GeographyEntity();

        $entity3->setGeography($dallas);
        $this->_em->persist($entity3);
        $this->_em->flush();
        $this->_em->clear();

        $query = $this->_em->createQuery('SELECT g, ST_Distance(g.geography, ST_GeomFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g');

        $query->setParameter('p1', $madison, 'geopoint');

        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($entity1, $result[0][0]);
        $this->assertEquals(1309106.31457703, $result[0][1]);
        $this->assertEquals($entity2, $result[1][0]);
        $this->assertEquals(2689041.41286683, $result[1][1]);
        $this->assertEquals($entity3, $result[2][0]);
        $this->assertEquals(1312731.61416563, $result[2][1]);
    }

    /**
     * @group geography
     */
    public function testSelectSTDistanceGeographyCartesian()
    {
        $newYork   = new GeographyPoint(-73.938611, 40.664167);
        $losAngles = new GeographyPoint(-118.2430, 34.0522);
        $dallas    = new GeographyPoint(-96.803889, 32.782778);
        $madison   = new GeographyPoint(-89.4, 43.066667);

        $entity1 = new GeographyEntity();

        $entity1->setGeography($newYork);
        $this->_em->persist($entity1);

        $entity2 = new GeographyEntity();

        $entity2->setGeography($losAngles);
        $this->_em->persist($entity2);

        $entity3 = new GeographyEntity();

        $entity3->setGeography($dallas);
        $this->_em->persist($entity3);
        $this->_em->flush();
        $this->_em->clear();

        $query = $this->_em->createQuery('SELECT g, ST_Distance(g.geography, ST_GeomFromText(:p1), false) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g');

        $query->setParameter('p1', $madison, 'geopoint');

        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($entity1, $result[0][0]);
        $this->assertEquals(1305895.94823465, $result[0][1]);
        $this->assertEquals($entity2, $result[1][0]);
        $this->assertEquals(2684082.08249337, $result[1][1]);
        $this->assertEquals($entity3, $result[2][0]);
        $this->assertEquals(1313754.60684762, $result[2][1]);
    }
}
