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

namespace CrEOF\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query;

/**
 * Abstract ORM test class
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class OrmTest extends \Doctrine\Tests\OrmFunctionalTestCase
{
    protected static $_setup = false;

    const GEOMETRY_ENTITY   = 'CrEOF\Tests\Fixtures\GeometryEntity';
    const POINT_ENTITY      = 'CrEOF\Tests\Fixtures\PointEntity';
    const LINESTRING_ENTITY = 'CrEOF\Tests\Fixtures\LineStringEntity';
    const POLYGON_ENTITY    = 'CrEOF\Tests\Fixtures\PolygonEntity';

    protected function setUp() {
        parent::setUp();

        if (!static::$_setup) {
            $conn = static::$_sharedConn;

            switch ($conn->getDatabasePlatform()->getName()) {
                case 'postgresql':
                    $this->setUpPostgreSql($conn);
                    break;
            }

            \Doctrine\DBAL\Types\Type::addType('geometry', '\CrEOF\DBAL\Types\Spatial\GeometryType');
            \Doctrine\DBAL\Types\Type::addType('point', '\CrEOF\DBAL\Types\Spatial\Geometry\PointType');
            \Doctrine\DBAL\Types\Type::addType('linestring', '\CrEOF\DBAL\Types\Spatial\Geometry\LineStringType');
            \Doctrine\DBAL\Types\Type::addType('polygon', '\CrEOF\DBAL\Types\Spatial\Geometry\PolygonType');

            $this->_schemaTool->createSchema(
                array(
                     $this->_em->getClassMetadata(self::GEOMETRY_ENTITY),
                     $this->_em->getClassMetadata(self::POINT_ENTITY),
                     $this->_em->getClassMetadata(self::LINESTRING_ENTITY),
                     $this->_em->getClassMetadata(self::POLYGON_ENTITY)
                ));
            static::$_setup = true;
        }
    }

    protected function tearDown()
    {
        parent::tearDown();

        $conn = static::$_sharedConn;

        $this->_sqlLoggerStack->enabled = false;

        $conn->executeUpdate('DELETE FROM GeometryEntity');
        $conn->executeUpdate('DELETE FROM PointEntity');
        $conn->executeUpdate('DELETE FROM LineStringEntity');
        $conn->executeUpdate('DELETE FROM PolygonEntity');

        $this->_em->clear();
    }

    private function setUpPostgreSql(Connection $conn)
    {
        $conn->exec('CREATE EXTENSION postgis');
    }
}
