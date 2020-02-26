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
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Helper\LineStringHelperTrait;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;

/**
 * GeometryWalker tests.
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
class GeometryWalkerTest extends OrmTestCase
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
        parent::setUp();
    }

    /**
     * Test the geometry walker binary.
     *
     * @group geometry
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testGeometryWalkerBinary()
    {
        $this->createStraightLineString();
        $this->createAngularLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        switch ($this->getPlatform()->getName()) {
            case 'mysql':
                $asBinary = 'AsBinary';
                $startPoint = 'StartPoint';
                $envelope = 'Envelope';
                break;
            default:
                $asBinary = 'ST_AsBinary';
                $startPoint = 'ST_StartPoint';
                $envelope = 'ST_Envelope';
                break;
        }

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l',
            $asBinary,
            $startPoint
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'CrEOF\Spatial\ORM\Query\GeometryWalker'
        );

        $result = $query->getResult();
        static::assertEquals(new Point(0, 0), $result[0][1]);
        static::assertEquals(new Point(3, 3), $result[1][1]);

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l',
            $asBinary,
            $envelope
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        static::assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[0][1]);
        static::assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[1][1]);
    }

    /**
     * Test the geometry walker.
     *
     * @group geometry
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testGeometryWalkerText()
    {
        $this->createStraightLineString();
        $this->createAngularLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        switch ($this->getPlatform()->getName()) {
            case 'mysql':
                $asText = 'AsText';
                $startPoint = 'StartPoint';
                $envelope = 'Envelope';
                break;
            default:
                $asText = 'ST_AsText';
                $startPoint = 'ST_StartPoint';
                $envelope = 'ST_Envelope';
                break;
        }

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l',
            $asText,
            $startPoint
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        static::assertEquals(new Point(0, 0), $result[0][1]);
        static::assertEquals(new Point(3, 3), $result[1][1]);

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM CrEOF\Spatial\Tests\Fixtures\LineStringEntity l',
            $asText,
            $envelope
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        static::assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[0][1]);
        static::assertInstanceOf('CrEOF\Spatial\PHP\Types\Geometry\Polygon', $result[1][1]);
    }
}
