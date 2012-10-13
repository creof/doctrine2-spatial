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

use CrEOF\Exception\UnsupportedPlatformException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Query;

/**
 * Abstract ORM test class
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class OrmTest extends \Doctrine\Tests\OrmFunctionalTestCase
{
    /**
     * @var bool
     */
    protected static $_setup = false;

    /**
     * @var Connection
     */
    protected static $_sharedConn;

    /**
     * @var AbstractPlatform
     */
    protected $platform;

    const GEOMETRY_ENTITY   = 'CrEOF\Tests\Fixtures\GeometryEntity';
    const POINT_ENTITY      = 'CrEOF\Tests\Fixtures\PointEntity';
    const LINESTRING_ENTITY = 'CrEOF\Tests\Fixtures\LineStringEntity';
    const POLYGON_ENTITY    = 'CrEOF\Tests\Fixtures\PolygonEntity';
    const GEOGRAPHY_ENTITY  = 'CrEOF\Tests\Fixtures\GeographyEntity';

    protected function setUp() {
        parent::setUp();

        $this->platform = static::$_sharedConn->getDatabasePlatform();

        if ( ! static::$_setup) {
            static::$_setup = true;

            switch ($this->platform->getName()) {
                case 'postgresql':
                    $this->setUpPostgreSql(static::$_sharedConn);
                    break;
                case 'mysql':
                    $this->setUpMySql(static::$_sharedConn);
                    break;
                default:
                    throw UnsupportedPlatformException::unsupportedPlatform($this->platform->getName());
                    break;
            }
        }
    }

    protected function setupCommonTypes()
    {
        \Doctrine\DBAL\Types\Type::addType('geometry', '\CrEOF\DBAL\Types\Spatial\GeometryType');
        \Doctrine\DBAL\Types\Type::addType('point', '\CrEOF\DBAL\Types\Spatial\Geometry\PointType');
        \Doctrine\DBAL\Types\Type::addType('linestring', '\CrEOF\DBAL\Types\Spatial\Geometry\LineStringType');
        \Doctrine\DBAL\Types\Type::addType('polygon', '\CrEOF\DBAL\Types\Spatial\Geometry\PolygonType');
    }

    protected function setupCommonEntities()
    {
        $this->_schemaTool->createSchema(
            array(
                $this->_em->getClassMetadata(self::GEOMETRY_ENTITY),
                $this->_em->getClassMetadata(self::POINT_ENTITY),
                $this->_em->getClassMetadata(self::LINESTRING_ENTITY),
                $this->_em->getClassMetadata(self::POLYGON_ENTITY),
            )
        );
    }

    protected function tearDownCommonEntities(Connection $conn)
    {
        $conn->executeUpdate('DELETE FROM GeometryEntity');
        $conn->executeUpdate('DELETE FROM PointEntity');
        $conn->executeUpdate('DELETE FROM LineStringEntity');
        $conn->executeUpdate('DELETE FROM PolygonEntity');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $conn = static::$_sharedConn;

        $this->_sqlLoggerStack->enabled = false;

        $this->tearDownCommonEntities($conn);

        switch ($conn->getDatabasePlatform()->getName()) {
            case 'postgresql':
                $this->tearDownPostgreSql($conn);
                break;
            case 'mysql':
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($this->platform->getName());
                break;
        }

        $this->_em->clear();
    }

    /**
     * @param Connection $conn
     */
    protected function setUpMySql(Connection $conn)
    {
        $this->setupCommonTypes();
        $this->setupCommonEntities();
    }


    /**
     * @param Connection $conn
     */
    protected function setUpPostgreSql(Connection $conn)
    {
        $conn->exec('CREATE EXTENSION postgis');

        $this->setupCommonTypes();
        \Doctrine\DBAL\Types\Type::addType('geography', '\CrEOF\DBAL\Types\Spatial\GeographyType');

        $this->setupCommonEntities();
        $this->_schemaTool->createSchema(
            array(
                $this->_em->getClassMetadata(self::GEOGRAPHY_ENTITY)
            ));
    }

    /**
     * @param Connection $conn
     */
    protected function tearDownPostgreSql(Connection $conn)
    {
        $conn->executeUpdate('DELETE FROM GeographyEntity');
    }
}
