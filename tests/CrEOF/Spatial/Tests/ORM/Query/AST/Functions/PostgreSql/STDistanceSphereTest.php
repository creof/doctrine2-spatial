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

namespace CrEOF\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use CrEOF\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\PointEntity;
use CrEOF\Spatial\Tests\Fixtures\GeographyEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\ORM\Query;

/**
 * ST_Distance_Sphere DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 */
class STDistanceSphereTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSelectSTDistanceSphereGeometry()
    {
        $newYork   = new Point(-73.938611, 40.664167);
        $losAngles = new Point(-118.2430, 34.0522);
        $dallas    = new Point(-96.803889, 32.782778);

        $entity1 = new PointEntity();

        $entity1->setPoint($newYork);
        $this->getEntityManager()->persist($entity1);

        $entity2 = new PointEntity();

        $entity2->setPoint($losAngles);
        $this->getEntityManager()->persist($entity2);

        $entity3 = new PointEntity();

        $entity3->setPoint($dallas);
        $this->getEntityManager()->persist($entity3);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query  = $this->getEntityManager()->createQuery('SELECT p, ST_Distance_Sphere(p.point, ST_GeomFromText(:p1)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

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
