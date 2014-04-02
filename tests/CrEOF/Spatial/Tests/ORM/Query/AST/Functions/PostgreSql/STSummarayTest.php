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

use CrEOF\Spatial\PHP\Types\Geography\LineString as GeographyLineString;
use CrEOF\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use CrEOF\Spatial\PHP\Types\Geography\Polygon as GeographyPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use CrEOF\Spatial\Tests\Fixtures\GeographyEntity;
use CrEOF\Spatial\Tests\OrmTest;
use Doctrine\ORM\Query;

/**
 * ST_Summary DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group postgresql
 * @group dql
 */
class STSummaryTest extends OrmTest
{
    protected function setUp()
    {
        $this->useEntity('geometry');
        $this->useEntity('geography');
        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSelectSTSummaryGeometry()
    {
        $entity1 = new GeometryEntity();
        $point1  = new Point(5, 5);

        $entity1->setGeometry($point1);
        $this->_em->persist($entity1);

        $entity2     = new GeometryEntity();
        $lineString2 = new LineString(
            array(
                array(1, 1),
                array(2, 2),
                array(3, 3)
            )
        );

        $entity2->setGeometry($lineString2);
        $this->_em->persist($entity2);

        $entity3  = new GeometryEntity();
        $polygon3 = new Polygon(
            array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            )
        );

        $entity3->setGeometry($polygon3);
        $this->_em->persist($entity3);
        $this->_em->flush();
        $this->_em->clear();

        $query  = $this->_em->createQuery('SELECT g, ST_Summary(g.geometry) FROM CrEOF\Spatial\Tests\Fixtures\GeometryEntity g');
        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($entity1, $result[0][0]);
        $this->assertRegExp('/^Point\[[^G]*\]/', $result[0][1]);
        $this->assertEquals($entity2, $result[1][0]);
        $this->assertRegExp('/^LineString\[[^G]*\]/', $result[1][1]);
        $this->assertEquals($entity3, $result[2][0]);
        $this->assertRegExp('/^Polygon\[[^G]*\]/', $result[2][1]);
    }

    /**
     * @group geography
     */
    public function testSelectSTSummaryGeography()
    {
        $entity1 = new GeographyEntity();
        $point1  = new GeographyPoint(5, 5);

        $entity1->setGeography($point1);
        $this->_em->persist($entity1);

        $entity2     = new GeographyEntity();
        $lineString2 = new GeographyLineString(
            array(
                array(1, 1),
                array(2, 2),
                array(3, 3)
            )
        );

        $entity2->setGeography($lineString2);
        $this->_em->persist($entity2);

        $entity3  = new GeographyEntity();
        $polygon3 = new GeographyPolygon(
            array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            )
        );

        $entity3->setGeography($polygon3);
        $this->_em->persist($entity3);
        $this->_em->flush();
        $this->_em->clear();

        $query  = $this->_em->createQuery('SELECT g, ST_Summary(g.geography) FROM CrEOF\Spatial\Tests\Fixtures\GeographyEntity g');
        $result = $query->getResult();

        $this->assertCount(3, $result);
        $this->assertEquals($entity1, $result[0][0]);
        $this->assertRegExp('/^Point\[.*G.*\]/', $result[0][1]);
        $this->assertEquals($entity2, $result[1][0]);
        $this->assertRegExp('/^LineString\[.*G.*\]/', $result[1][1]);
        $this->assertEquals($entity3, $result[2][0]);
        $this->assertRegExp('/^Polygon\[.*G.*\]/', $result[2][1]);
    }
}
