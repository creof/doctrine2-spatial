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
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\ORM\Query;

/**
 * GLength DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 */
class GLengthTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSelectGLength()
    {
        $entity = new LineStringEntity();

        $entity->setLineString(new LineString(array(
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2)
        )));

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query  = $this->getEntityManager()->createQuery('SELECT l, GLength(l.lineString) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l');
        $result = $query->getResult();

        $this->assertCount(1, $result);
        $this->assertEquals($entity, $result[0][0]);
        $this->assertEquals(2.82842712474619, $result[0][1]);
    }

    /**
     * @group geometry
     */
    public function testGLengthWhereParameter()
    {
        $entity = new LineStringEntity();

        $entity->setLineString(new LineString(array(
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2)
        )));

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query  = $this->getEntityManager()->createQuery('SELECT l FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l WHERE GLength(GeomFromText(:p1)) > GLength(l.lineString)');

        $query->setParameter('p1', 'LINESTRING(0 0,1 1,2 2,3 3,4 4,5 5)', 'string');

        $result = $query->getResult();

        $this->assertCount(1, $result);
        $this->assertEquals($entity, $result[0]);
    }
}
