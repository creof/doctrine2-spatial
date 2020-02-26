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
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\PointEntity;
use CrEOF\Spatial\Tests\Helper\PointHelperTrait;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

/**
 * ST_SnapToGrid DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class STSnapToGridTest extends OrmTestCase
{
    use PointHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function with 2 parameters to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature2Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 0.5)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 2.5)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with three parameters to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature3Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 0.5, 1)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 3)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with five parameters to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature5Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 5.55, 6.25, 0.5, 0.5)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
            // phpcs:enable
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.05 6.75)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with six paramters to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature6Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), geometry(p.point), 0.005, 0.025, 0.5, 0.01)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
            // phpcs:enable
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.25 6.55)'],
        ];

        static::assertEquals($expected, $result);
    }
}
