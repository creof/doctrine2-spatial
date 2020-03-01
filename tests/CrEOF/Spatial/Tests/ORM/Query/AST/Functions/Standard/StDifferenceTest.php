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

namespace CrEOF\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\Tests\Helper\LineStringHelperTrait;
use CrEOF\Spatial\Tests\Helper\PointHelperTrait;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

/**
 * ST_Difference DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class StDifferenceTest extends OrmTestCase
{
    use LineStringHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform('postgresql');
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStDifference()
    {
        $lineStringA = $this->createLineStringA();
        $lineStringB = $this->createLineStringB();
        $lineStringC = $this->createLineStringC();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT l, ST_AsText(ST_Difference(ST_GeomFromText(:p), l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l'
            // phpcs:enable
        );

        $query->setParameter('p', 'LINESTRING(0 0, 12 12)', 'string');

        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($lineStringA, $result[0][0]);
        static::assertEquals('LINESTRING(10 10,12 12)', $result[0][1]);
        static::assertEquals($lineStringB, $result[1][0]);
        switch ($this->getPlatform()->getName()) {
            case 'mysql':
                //MySQL failed ST_Difference implementation, so I test the bad result.
                static::assertEquals('LINESTRING(0 0,12 12)', $result[1][1]);
                break;
            case 'postgresl':
            default:
                //Here is the good result.
                // A linestring minus another crossing linestring returns initial linestring splited
                static::assertEquals('MULTILINESTRING((0 0,6 6),(6 6,12 12))', $result[1][1]);
        }
        static::assertEquals($lineStringC, $result[2][0]);
        static::assertEquals('LINESTRING(0 0,12 12)', $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testStDifferenceWhereParameter()
    {
        $this->createLineStringA();
        $lineStringB = $this->createLineStringB();
        $lineStringC = $this->createLineStringC();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT l FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l WHERE ST_IsEmpty(ST_Difference(ST_GeomFromText(:p1), l.lineString)) = false'
            // phpcs:enable
        );

        $query->setParameter('p1', 'LINESTRING(0 0, 10 10)', 'string');

        $result = $query->getResult();

        static::assertCount(2, $result);
        static::assertEquals($lineStringB, $result[0]);
        static::assertEquals($lineStringC, $result[1]);
    }
}
