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

namespace CrEOF\Spatial\Tests;

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
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
     * @var Connection
     */
    protected $conn;

    /**
     * @var string
     */
    protected $platformName;

    const GEOMETRY_ENTITY   = 'CrEOF\Spatial\Tests\Fixtures\GeometryEntity';
    const POINT_ENTITY      = 'CrEOF\Spatial\Tests\Fixtures\PointEntity';
    const LINESTRING_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\LineStringEntity';
    const POLYGON_ENTITY    = 'CrEOF\Spatial\Tests\Fixtures\PolygonEntity';
    const GEOGRAPHY_ENTITY  = 'CrEOF\Spatial\Tests\Fixtures\GeographyEntity';

    /**
     * @throws UnsupportedPlatformException
     */
    protected function setUp()
    {
        parent::setUp();

        $this->conn         = static::$_sharedConn;
        $this->platformName = $this->conn->getDatabasePlatform()->getName();

        /**
         * Add types to DBAL and setup fixtures
         */
        if ( ! static::$_setup) {
            static::$_setup = true;

            switch ($this->platformName) {
                case 'postgresql':
                    $this->setUpPostgreSql();
                    break;
                case 'mysql':
                    $this->setUpMySql();
                    break;
                default:
                    throw UnsupportedPlatformException::unsupportedPlatform($this->platformName);
                    break;
            }
        }

        /**
         * Add DQL functions to ORM
         */
        $this->setupCommonFunctions();

        switch ($this->platformName) {
            case 'postgresql':
                $this->setUpPostgreSqlFunctions();
                break;
            case 'mysql':
                $this->setUpMySqlFunctions();
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($this->platformName);
                break;
        }
    }

    /**
     * @throws UnsupportedPlatformException
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->_sqlLoggerStack->enabled = false;

        $this->tearDownCommonEntities();

        switch ($this->platformName) {
            case 'postgresql':
                $this->tearDownPostgreSql();
                break;
            case 'mysql':
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($this->platformName);
                break;
        }

        $this->_em->clear();
    }

    /**
     * Add types to DBAL common to all platforms
     */
    protected function setupCommonTypes()
    {
        \Doctrine\DBAL\Types\Type::addType('geometry', 'CrEOF\Spatial\DBAL\Types\GeometryType');
        \Doctrine\DBAL\Types\Type::addType('point', 'CrEOF\Spatial\DBAL\Types\Geometry\PointType');
        \Doctrine\DBAL\Types\Type::addType('linestring', 'CrEOF\Spatial\DBAL\Types\Geometry\LineStringType');
        \Doctrine\DBAL\Types\Type::addType('polygon', 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType');
    }

    /**
     * Add DQL functions to ORM common to all platforms
     */
    protected function setupCommonFunctions()
    {
    }

    /**
     * Setup fixtures common to all platforms
     */
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

    protected function tearDownCommonEntities()
    {
        $this->conn->executeUpdate('DELETE FROM GeometryEntity');
        $this->conn->executeUpdate('DELETE FROM PointEntity');
        $this->conn->executeUpdate('DELETE FROM LineStringEntity');
        $this->conn->executeUpdate('DELETE FROM PolygonEntity');
    }

    protected function setUpMySql()
    {
        $this->setupCommonTypes();
        $this->setupCommonEntities();
    }

    protected function setUpPostgreSql()
    {
        $this->conn->exec('CREATE EXTENSION postgis');

        $this->setupCommonTypes();
        \Doctrine\DBAL\Types\Type::addType('geography', 'CrEOF\Spatial\DBAL\Types\GeographyType');

        $this->setupCommonFunctions();

        $this->setupCommonEntities();
        $this->_schemaTool->createSchema(
            array(
                $this->_em->getClassMetadata(self::GEOGRAPHY_ENTITY)
            )
        );
    }

    protected function setUpPostgreSqlFunctions()
    {
        $this->_em->getConfiguration()->addCustomStringFunction('st_asbinary', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsBinary');
        $this->_em->getConfiguration()->addCustomStringFunction('st_astext', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsText');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_area', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STArea');
        $this->_em->getConfiguration()->addCustomStringFunction('st_centroid', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCentroid');
        $this->_em->getConfiguration()->addCustomStringFunction('st_closestpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STClosestPoint');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_contains', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContains');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_containsproperly', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContainsProperly');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_covers', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCovers');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_coveredby', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCoveredBy');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_crosses', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCrosses');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_disjoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDisjoint');
        $this->_em->getConfiguration()->addCustomStringFunction('st_envelope', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STEnvelope');
        $this->_em->getConfiguration()->addCustomStringFunction('st_geomfromtext', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromText');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_length', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLength');
        $this->_em->getConfiguration()->addCustomNumericFunction('st_linecrossingdirection', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineCrossingDirection');
        $this->_em->getConfiguration()->addCustomStringFunction('st_startpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STStartPoint');
    }

    protected function setUpMySqlFunctions()
    {
        $this->_em->getConfiguration()->addCustomNumericFunction('area', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Area');
        $this->_em->getConfiguration()->addCustomStringFunction('asbinary', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsBinary');
        $this->_em->getConfiguration()->addCustomStringFunction('astext', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsText');
        $this->_em->getConfiguration()->addCustomNumericFunction('contains', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Contains');
        $this->_em->getConfiguration()->addCustomNumericFunction('disjoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Disjoint');
        $this->_em->getConfiguration()->addCustomStringFunction('envelope', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Envelope');
        $this->_em->getConfiguration()->addCustomStringFunction('geomfromtext', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeomFromText');
        $this->_em->getConfiguration()->addCustomNumericFunction('glength', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GLength');
        $this->_em->getConfiguration()->addCustomNumericFunction('mbrcontains', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRContains');
        $this->_em->getConfiguration()->addCustomNumericFunction('mbrdisjoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRDisjoint');
        $this->_em->getConfiguration()->addCustomStringFunction('startpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\StartPoint');
    }

    protected function tearDownPostgreSql()
    {
        $this->conn->executeUpdate('DELETE FROM GeographyEntity');
    }
}
