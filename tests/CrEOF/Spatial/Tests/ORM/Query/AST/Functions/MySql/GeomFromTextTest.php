<?php
/**
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

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\ORM\Query;

/**
 * GeomFromText DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 */
class GeomFromTextTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesType('point');
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testPoint()
    {
        $entity1 = new GeometryEntity();

        $entity1->setGeometry(new Point(5, 5));
        $this->getEntityManager()->persist($entity1);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query  = $this->getEntityManager()->createQuery('SELECT g FROM CrEOF\Spatial\Tests\Fixtures\GeometryEntity g WHERE g.geometry = GeomFromText(:p1)');

        $query->setParameter('p1', 'POINT(5 5)', 'string');

        $result = $query->getResult();

        $this->assertCount(1, $result);
        $this->assertEquals($entity1, $result[0]);
    }

    /**
     * @group geometry
     */
    public function testLineString()
    {
        $value = array(
            new Point(0, 0),
            new Point(5, 5),
            new Point(10, 10)
        );

        $entity1 = new GeometryEntity();

        $entity1->setGeometry(new LineString($value));
        $this->getEntityManager()->persist($entity1);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT g FROM CrEOF\Spatial\Tests\Fixtures\GeometryEntity g WHERE g.geometry = GeomFromText(:p1)');

        $query->setParameter('p1', 'LINESTRING(0 0,5 5,10 10)', 'string');

        $result = $query->getResult();

        $this->assertCount(1, $result);
        $this->assertEquals($entity1, $result[0]);
    }
}
