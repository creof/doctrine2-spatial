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

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\ORM\Query;

/**
 * ST_AsBinary DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 */
class STAsBinaryTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSTAsBinary()
    {
        $lineString1 = array(
            new Point(0, 0),
            new Point(2, 2),
            new Point(5, 5)
        );
        $lineString2 = array(
            new Point(3, 3),
            new Point(4, 15),
            new Point(5, 22)
        );
        $entity1 = new LineStringEntity();

        $entity1->setLineString(new LineString($lineString1));
        $this->getEntityManager()->persist($entity1);

        $entity2 = new LineStringEntity();

        $entity2->setLineString(new LineString($lineString2));
        $this->getEntityManager()->persist($entity2);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query  = $this->getEntityManager()->createQuery('SELECT ST_AsBinary(l.lineString) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l');
        $result = $query->getResult();

        $this->assertEquals('010200000003000000000000000000000000000000000000000000000000000040000000000000004000000000000014400000000000001440', bin2hex(stream_get_contents($result[0][1])));
        $this->assertEquals('0102000000030000000000000000000840000000000000084000000000000010400000000000002e4000000000000014400000000000003640', bin2hex(stream_get_contents($result[1][1])));
    }
}
