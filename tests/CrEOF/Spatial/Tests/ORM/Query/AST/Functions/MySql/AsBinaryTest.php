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

namespace CrEOF\Spatial\Tests\ORM\Functions\MySql;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;
use CrEOF\Spatial\Tests\OrmTest;
use Doctrine\ORM\Query;

/**
 * AsBinary DQL function tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group mysql
 * @group dql
 */
class AsBinaryTest extends OrmTest
{
    protected function setUp()
    {
        $this->useEntity('linestring');
        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testAsBinary()
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
        $this->_em->persist($entity1);

        $entity2 = new LineStringEntity();

        $entity2->setLineString(new LineString($lineString2));
        $this->_em->persist($entity2);
        $this->_em->flush();
        $this->_em->clear();

        $query   = $this->_em->createQuery('SELECT AsBinary(l.lineString) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l');
        $result  = $query->getResult();
        $string1 = '010200000003000000000000000000000000000000000000000000000000000040000000000000004000000000000014400000000000001440';
        $string2 = '0102000000030000000000000000000840000000000000084000000000000010400000000000002e4000000000000014400000000000003640';
        $binary1 = pack('H*', $string1);
        $binary2 = pack('H*', $string2);

        $this->assertEquals($binary1, $result[0][1]);
        $this->assertEquals($binary2, $result[1][1]);
    }
}
