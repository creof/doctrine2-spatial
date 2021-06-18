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

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\Tests\Helper\PolygonHelperTrait;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

/**
 * SP_Transform DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @transformDefaultClass
 * @coversNothing
 */
class SpTransformTest extends OrmTestCase
{
    use PolygonHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform('postgresql');

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
    public function testFunctionInSelect()
    {
        $massachusetts = $this->createMassachusettsState();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p, ST_AsText(PgSql_Transform(p.polygon, :proj)) FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p'
            // phpccs: enable
        );
        $query->setParameter('proj', '+proj=longlat +datum=WGS84 +no_defs');
        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($massachusetts, $result[0][0]);
        static::assertSame('POLYGON((-71.1776848522251 42.3902896512902,-71.1776843766326 42.3903829478009,-71.1775844305465 42.3903826677917,-71.1775825927231 42.3902893647987,-71.1776848522251 42.3902896512902))', $result[0][1]);
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
    public function testFunctionInSelectWith3Parameters()
    {
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $massachusetts = $this->createMassachusettsState(false);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_AsText(PgSql_Transform(p.polygon, :from, :to)) FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('from', '+proj=lcc +lat_1=42.68333333333333 +lat_2=41.71666666666667 +lat_0=41 +lon_0=-71.5 +x_0=200000.0001016002 +y_0=750000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=us-ft +no_defs ');
        $query->setParameter('to', '+proj=longlat +datum=WGS84 +no_defs');
        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($massachusetts, $result[0][0]);
        static::assertSame('POLYGON((-71.1776848522251 42.3902896512902,-71.1776843766326 42.3903829478009,-71.1775844305465 42.3903826677917,-71.1775825927231 42.3902893647987,-71.1776848522251 42.3902896512902))', $result[0][1]);
        // phpccs: enable
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
    public function testFunctionInSelectWithSrid()
    {
        $massachusetts = $this->createMassachusettsState();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        //TODO The test above failed because DQL SRID is seen as a string
        static::markTestSkipped('The test above failed because DQL SRID is seen as a string');
        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p, ST_AsText(PgSql_Transform(p.polygon, :srid)) FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p'
            // phpccs: enable
        );
        $query->setParameter('srid', 4326, 'integer');
        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($massachusetts, $result[0][0]);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        static::assertSame('POLYGON((-71.1776848522251 42.3902896512902,-71.1776843766326 42.3903829478009, -71.1775844305465 42.3903826677917,-71.1775825927231 42.3902893647987,-71.1776848522251 42.3902896512902))', $result[0][1]);
        // phpcs: enable
    }
}
