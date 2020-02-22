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

namespace CrEOF\Spatial\Tests\ORM\Query;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use CrEOF\Spatial\Tests\Fixtures\PolygonEntity;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Version;

/**
 * DQL type wrapping tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class WrappingTest extends OrmTestCase
{
    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesType('point');
        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testTypeWrappingSelect()
    {
        $lineString = new LineString([
            new Point(0, 0),
            new Point(10, 0),
            new Point(10, 10),
            new Point(0, 10),
            new Point(0, 0),
        ]);
        $entity = new PolygonEntity();

        $entity->setPolygon(new Polygon([$lineString]));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $dql = 'SELECT p, %s(p.polygon, :geometry) FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p';

        switch ($this->getPlatform()->getName()) {
            case 'postgresql':
                $function = 'ST_Contains';
                break;
            case 'mysql':
                $function = 'Contains';
                break;
            default:
                //TODO create a static function to throw exception.
                throw new UnsupportedPlatformException(sprintf(
                    'DBAL platform "%s" is not currently supported.',
                    $this->getPlatform()->getName()
                ));
        }

        $dql = sprintf($dql, $function);

        $query = $this->getEntityManager()->createQuery($dql);

        $query->setParameter('geometry', new Point(2, 2), 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();
        $parameter = '?';

        if (Version::compare('2.5') <= 0) {
            $parameter = Type::getType('point')->convertToDatabaseValueSql($parameter, $this->getPlatform());
        }

        $regex = preg_quote(sprintf('/.polygon, %s)/', $parameter));

        $this->assertRegExp($regex, $result);
    }

    /**
     * @group geometry
     */
    public function testTypeWrappingWhere()
    {
        $entity = new GeometryEntity();

        $entity->setGeometry(new Point(5, 5));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT g FROM CrEOF\Spatial\Tests\Fixtures\GeometryEntity g WHERE g.geometry = :geometry');

        $query->setParameter('geometry', new Point(5, 5), 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();
        $parameter = '?';

        if (Version::compare('2.5') <= 0) {
            $parameter = Type::getType('point')->convertToDatabaseValueSql($parameter, $this->getPlatform());
        }

        $regex = preg_quote(sprintf('/geometry = %s/', $parameter));

        $this->assertRegExp($regex, $result);
    }
}
