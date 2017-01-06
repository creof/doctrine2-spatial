<?php

/**
 * Copyright (C) 2017 Derek J. Lambert
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

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\Tests\Fixtures\PolygonEntity;
use CrEOF\Spatial\Tests\OrmTestCase;

/**
 * ST_Overlaps DQL function tests
 *
 * @author Dragos Protung
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 */
class STOverlapsTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSelectSTMakeEnvelope()
    {
        $lineString1 = new LineString(array(
            new Point(0, 0),
            new Point(2, 0),
            new Point(2, 2),
            new Point(0, 2),
            new Point(0, 0)
        ));
        $lineString2 = new LineString(array(
            new Point(2, 2),
            new Point(7, 2),
            new Point(7, 7),
            new Point(2, 7),
            new Point(2, 2)
        ));
        $entity1 = new PolygonEntity();

        $entity1->setPolygon(new Polygon(array($lineString1)));
        $this->getEntityManager()->persist($entity1);

        $entity2 = new PolygonEntity();

        $entity2->setPolygon(new Polygon(array($lineString2)));
        $this->getEntityManager()->persist($entity2);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p as point, ST_Overlaps(p.polygon, ST_Buffer(ST_GeomFromText(:p1), 3)) as overlap FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('p1', 'POINT(0 0)', 'string');
        $result = $query->getResult();

        $this->assertCount(2, $result);
        $this->assertEquals($entity1, $result[0]['point']);
        $this->assertFalse($result[0]['overlap']);
        $this->assertEquals($entity2, $result[1]['point']);
        $this->assertTrue($result[1]['overlap']);
    }
}
