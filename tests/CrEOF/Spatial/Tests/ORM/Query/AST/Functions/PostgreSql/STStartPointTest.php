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

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;
use CrEOF\Spatial\Tests\OrmTest;
use Doctrine\ORM\Query;

/**
 * ST_StartPoint DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group postgresql
 * @group dql
 */
class STStartPointTest extends OrmTest
{
    protected function setUp()
    {
        $this->useEntity('linestring');
        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSTStartPointSelect()
    {
        $lineString1 = new LineString(array(
            new Point(0, 0),
            new Point(2, 2),
            new Point(5, 5)
        ));
        $entity1 = new LineStringEntity();

        $entity1->setLineString($lineString1);
        $this->_em->persist($entity1);
        $this->_em->flush();
        $this->_em->clear();

        $query = $this->_em->createQuery('SELECT ST_AsText(ST_StartPoint(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l');

        $result = $query->getResult();

        $this->assertEquals('POINT(0 0)', $result[0][1]);
    }

    /**
     * @group geometry
     */
    public function testSTStartPointWhereComparePoint()
    {
        $lineString1 = new LineString(array(
            new Point(0, 0),
            new Point(2, 2),
            new Point(5, 5)
        ));
        $lineString2 = new LineString(array(
            new Point(3, 3),
            new Point(4, 15),
            new Point(5, 22)
        ));
        $entity1 = new LineStringEntity();

        $entity1->setLineString($lineString1);
        $this->_em->persist($entity1);

        $entity2 = new LineStringEntity();

        $entity2->setLineString($lineString2);
        $this->_em->persist($entity2);
        $this->_em->flush();
        $this->_em->clear();

        $query = $this->_em->createQuery('SELECT l FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l WHERE ST_StartPoint(l.lineString) = ST_GeomFromText(:p1)');

        $query->setParameter('p1', new Point(0, 0), 'point');

        $result = $query->getResult();

        $this->assertCount(1, $result);
        $this->assertEquals($entity1, $result[0]);
    }

    /**
     * @group geometry
     */
    public function testSTStartPointWhereCompareLineString()
    {
        $lineString1 = new LineString(array(
            new Point(0, 0),
            new Point(2, 2),
            new Point(5, 5)
        ));
        $lineString2 = new LineString(array(
            new Point(3, 3),
            new Point(4, 15),
            new Point(5, 22)
        ));
        $entity1 = new LineStringEntity();

        $entity1->setLineString($lineString1);
        $this->_em->persist($entity1);

        $entity2 = new LineStringEntity();

        $entity2->setLineString($lineString2);
        $this->_em->persist($entity2);
        $this->_em->flush();
        $this->_em->clear();

        $query = $this->_em->createQuery('SELECT l FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l WHERE ST_StartPoint(l.lineString) = ST_StartPoint(ST_GeomFromText(:p1))');

        $query->setParameter('p1', $lineString2, 'linestring');

        $result = $query->getResult();

        $this->assertCount(1, $result);
        $this->assertEquals($entity2, $result[0]);
    }
}
