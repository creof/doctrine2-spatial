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

namespace CrEOF\Spatial\Tests\ORM\Query\AST\Functions\MySql;

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\LineStringEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\ORMException;

/**
 * AsBinary DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class AsBinaryTest extends OrmTestCase
{
    /**
     * Setup the test.
     *
     * @throws UnsupportedPlatformException this should not happen
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * Test to convert as binary.
     *
     * @group geometry
     *
     * @throws ORMException     this should not happen
     * @throws MappingException this should not happen
     */
    public function testAsBinary()
    {
        $lineStringA = [
            new Point(0, 0),
            new Point(2, 2),
            new Point(5, 5),
        ];
        $lineStringB = [
            new Point(3, 3),
            new Point(4, 15),
            new Point(5, 22),
        ];
        $entityA = new LineStringEntity();

        $entityA->setLineString(new LineString($lineStringA));
        $this->getEntityManager()->persist($entityA);

        $entityB = new LineStringEntity();

        $entityB->setLineString(new LineString($lineStringB));
        $this->getEntityManager()->persist($entityB);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT AsBinary(l.lineString) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $result = $query->getResult();
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $stringA = '010200000003000000000000000000000000000000000000000000000000000040000000000000004000000000000014400000000000001440';
        $stringB = '0102000000030000000000000000000840000000000000084000000000000010400000000000002e4000000000000014400000000000003640';
        // phpcs:enable
        $binaryA = pack('H*', $stringA);
        $binaryB = pack('H*', $stringB);

        $this->assertEquals($binaryA, $result[0][1]);
        $this->assertEquals($binaryB, $result[1][1]);
    }
}
