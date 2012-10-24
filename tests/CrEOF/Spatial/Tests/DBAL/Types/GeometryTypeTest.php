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

namespace CrEOF\Spatial\Tests\DBAL\Types;

use Doctrine\ORM\Query;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\Tests\OrmTest;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use CrEOF\Spatial\Tests\Fixtures\NoHintGeometryEntity;

/**
 * Doctrine GeometryType tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group common
 */
class GeometryTypeTest extends OrmTest
{
    public function testNullGeometry()
    {
        $entity = new GeometryEntity();

        $this->_em->persist($entity);
        $this->_em->flush();

        $id = $entity->getId();

        $this->_em->clear();

        $queryEntity = $this->_em->getRepository(self::GEOMETRY_ENTITY)->find($id);

        $this->assertEquals($entity, $queryEntity);
    }

    public function testPointGeometry()
    {
        $entity = new GeometryEntity();

        $entity->setGeometry(new Point(1, 1));
        $this->_em->persist($entity);
        $this->_em->flush();

        $id = $entity->getId();

        $this->_em->clear();

        $queryEntity = $this->_em->getRepository(self::GEOMETRY_ENTITY)->find($id);

        $this->assertEquals($entity, $queryEntity);
    }

    public function testLineStringGeometry()
    {
        $entity = new GeometryEntity();

        $entity->setGeometry(new LineString(
            array(
                 new Point(0, 0),
                 new Point(1, 1)
            ))
        );
        $this->_em->persist($entity);
        $this->_em->flush();

        $id = $entity->getId();

        $this->_em->clear();

        $queryEntity = $this->_em->getRepository(self::GEOMETRY_ENTITY)->find($id);

        $this->assertEquals($entity, $queryEntity);
    }

    public function testPolygonGeometry()
    {
        $entity = new GeometryEntity();

        $rings = array(
            new LineString(array(
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0)
            ))
        );

        $entity->setGeometry(new Polygon($rings));
        $this->_em->persist($entity);
        $this->_em->flush();

        $id = $entity->getId();

        $this->_em->clear();

        $queryEntity = $this->_em->getRepository(self::GEOMETRY_ENTITY)->find($id);

        $this->assertEquals($entity, $queryEntity);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Geometry columns require Geometry values
     */
    public function testBadGeometryValue()
    {
        $entity = new NoHintGeometryEntity();

        $entity->setGeometry('POINT(0 0)');
        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
