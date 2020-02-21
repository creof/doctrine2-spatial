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

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\PointEntity;
use CrEOF\Spatial\Tests\OrmTestCase;

/**
 * ST_SnapToGrid DQL function tests.
 *
 * @author  Dragos Protung
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class STSnapToGridTest extends OrmTestCase
{
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature2Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 0.5)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 2.5)'],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature3Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 0.5, 1)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 3)'],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature5Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 5.55, 6.25, 0.5, 0.5)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.05 6.75)'],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature6Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), geometry(p.point), 0.005, 0.025, 0.5, 0.01)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.25 6.55)'],
        ];

        $this->assertEquals($expected, $result);
    }
}
