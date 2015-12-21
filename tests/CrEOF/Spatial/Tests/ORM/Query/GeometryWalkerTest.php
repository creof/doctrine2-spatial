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

namespace CrEOF\Spatial\Tests\ORM\Query;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\ORM\Query;

/**
 * GeometryWalker tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 */
class GeometryWalkerTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testGeometryWalkerBinary()
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
        $this->getEntityManager()->persist($entity1);

        $entity2 = new LineStringEntity();

        $entity2->setLineString($lineString2);
        $this->getEntityManager()->persist($entity2);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        switch ($this->getPlatform()->getName()) {
            case 'mysql':
                $asBinary   = 'AsBinary';
                $startPoint = 'StartPoint';
                $envelope   = 'Envelope';
                break;
            default:
                $asBinary   = 'ST_AsBinary';
                $startPoint = 'ST_StartPoint';
                $envelope   = 'ST_Envelope';
                break;
        }

        $queryString = sprintf('SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l', $asBinary, $startPoint);
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        $this->assertEquals(new Point(0, 0), $result[0][1]);
        $this->assertEquals(new Point(3, 3), $result[1][1]);

        $queryString = sprintf('SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l', $asBinary, $envelope);
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        $this->assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[0][1]);
        $this->assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[1][1]);
    }

    /**
     * @group geometry
     */
    public function testGeometryWalkerText()
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
        $this->getEntityManager()->persist($entity1);

        $entity2 = new LineStringEntity();

        $entity2->setLineString($lineString2);
        $this->getEntityManager()->persist($entity2);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        switch ($this->getPlatform()->getName()) {
            case 'mysql':
                $asText   = 'AsText';
                $startPoint = 'StartPoint';
                $envelope   = 'Envelope';
                break;
            default:
                $asText   = 'ST_AsText';
                $startPoint = 'ST_StartPoint';
                $envelope   = 'ST_Envelope';
                break;
        }

        $queryString = sprintf('SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l', $asText, $startPoint);
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        $this->assertEquals(new Point(0, 0), $result[0][1]);
        $this->assertEquals(new Point(3, 3), $result[1][1]);

        $queryString = sprintf('SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l', $asText, $envelope);
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        $this->assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[0][1]);
        $this->assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[1][1]);
    }
}
