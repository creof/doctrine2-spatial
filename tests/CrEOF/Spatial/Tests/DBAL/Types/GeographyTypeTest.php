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

namespace CrEOF\Spatial\Tests\DBAL\Types;

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geography\LineString;
use CrEOF\Spatial\PHP\Types\Geography\Point;
use CrEOF\Spatial\PHP\Types\Geography\Polygon;
use CrEOF\Spatial\Tests\Fixtures\GeographyEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\Error\Error;

/**
 * Doctrine GeographyType tests.
 *
 * @group geography
 *
 * @internal
 * @coversDefaultClass
 */
class GeographyTypeTest extends OrmTestCase
{
    /**
     * Setup the geography type test.
     *
     * @throws UnsupportedPlatformException
     * @throws DBALException
     * @throws ORMException
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOGRAPHY_ENTITY);

        parent::setUp();
    }

    /**
     * Test to store and retrieve a geography composed by a linestring.
     *
     * @throws DBALException
     * @throws ORMException
     * @throws UnsupportedPlatformException
     * @throws MappingException
     * @throws OptimisticLockException
     */
    public function testLineStringGeography()
    {
        $entity = new GeographyEntity();

        $entity->setGeography(new LineString([
            new Point(0, 0),
            new Point(1, 1),
        ]));
        $this->storeAndRetrieve($entity);
    }

    /**
     * Test to store and retrieve a null geography.
     *
     * @throws DBALException
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnsupportedPlatformException
     */
    public function testNullGeography()
    {
        $entity = new GeographyEntity();
        $this->storeAndRetrieve($entity);
    }

    /**
     * Test to store and retrieve a geography composed by a single point.
     *
     * @throws DBALException
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnsupportedPlatformException
     */
    public function testPointGeography()
    {
        $entity = new GeographyEntity();

        $entity->setGeography(new Point(1, 1));
        $this->storeAndRetrieve($entity);
    }

    /**
     * Test to store and retrieve a geography composed by a polygon.
     *
     * @throws DBALException
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnsupportedPlatformException
     */
    public function testPolygonGeography()
    {
        $entity = new GeographyEntity();

        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
        ];

        $entity->setGeography(new Polygon($rings));
        $this->storeAndRetrieve($entity);
    }

    /**
     * Store and retrieve geography entity in database.
     * Then assert data are equals, not same.
     *
     * @param GeographyEntity $entity Entity to test
     *
     * @throws DBALException
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnsupportedPlatformException
     */
    private function storeAndRetrieve(GeographyEntity $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOGRAPHY_ENTITY)->find($id);

        self::assertEquals($entity, $queryEntity);
    }
}
