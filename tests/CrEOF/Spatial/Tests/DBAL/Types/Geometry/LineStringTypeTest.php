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

namespace CrEOF\Spatial\Tests\DBAL\Types\Geometry;

use Doctrine\ORM\Query;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\OrmTestCase;
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;

/**
 * Doctrine LineStringType tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group geometry
 */
class LineStringTypeTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        parent::setUp();
    }

    public function testNullLineStringType()
    {
        $entity = new LineStringEntity();

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::LINESTRING_ENTITY)->find($id);

        $this->assertEquals($entity, $queryEntity);
    }

    public function testLineString()
    {
        $lineString = new LineString(
            array(
                new Point(0, 0),
                new Point(1, 1),
                new Point(2, 2)
            )
        );
        $entity = new LineStringEntity();

        $entity->setLineString($lineString);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::LINESTRING_ENTITY)->find($id);

        $this->assertEquals($entity, $queryEntity);
    }

    public function testFindByLineString()
    {
        $lineString = new LineString(
            array(
                 new Point(0, 0),
                 new Point(1, 1),
                 new Point(2, 2)
            )
        );
        $entity = new LineStringEntity();

        $entity->setLineString($lineString);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $result = $this->getEntityManager()->getRepository(self::LINESTRING_ENTITY)->findByLineString($lineString);

        $this->assertEquals($entity, $result[0]);
    }
}
