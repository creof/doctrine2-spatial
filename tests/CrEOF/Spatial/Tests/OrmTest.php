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
use Doctrine\DBAL\Types\Type;
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

    const GEOMETRY_ENTITY         = 'CrEOF\Spatial\Tests\Fixtures\GeometryEntity';
    const NO_HINT_GEOMETRY_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\NoHintGeometryEntity';
    const POINT_ENTITY            = 'CrEOF\Spatial\Tests\Fixtures\PointEntity';
    const LINESTRING_ENTITY       = 'CrEOF\Spatial\Tests\Fixtures\LineStringEntity';
    const POLYGON_ENTITY          = 'CrEOF\Spatial\Tests\Fixtures\PolygonEntity';
    const GEOGRAPHY_ENTITY        = 'CrEOF\Spatial\Tests\Fixtures\GeographyEntity';

    /**
     * @throws UnsupportedPlatformException
     */
    protected function setUp()
    {
        parent::setUp();

        $this->conn = static::$_sharedConn;

        if ( ! static::$_setup) {
            $this->conn->getConfiguration()->setSQLLogger(new \CrEOF\Spatial\Tests\FileSQLLogger('/Users/dlambert/Development/doctrine2-spatial/logger.log'));

            static::$_setup = true;

            $this->setupPlatform();
            $this->setupTypes();
            $this->setupEntities();
        }

        $this->setupFunctions();
    }

    /**
     * Perform any platform specific setup
     *
     * @group mysql
     *
     * @throws \CrEOF\Spatial\Exception\UnsupportedPlatformException
     */
    protected function setupPlatform()
    {
        switch ($this->getPlatform()->getName()) {
            case 'postgresql':
                $this->conn->exec('CREATE EXTENSION postgis');
                break;
            case 'mysql':
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($this->getPlatform()->getName());
                break;
        }
    }

    /**
     * Add types to DBAL
     */
    protected function setupTypes()
    {
        // Geometry
        Type::addType('geometry', 'CrEOF\Spatial\DBAL\Types\GeometryType');
        Type::addType('point', 'CrEOF\Spatial\DBAL\Types\Geometry\PointType');
        Type::addType('linestring', 'CrEOF\Spatial\DBAL\Types\Geometry\LineStringType');
        Type::addType('polygon', 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType');

        // Geography
        Type::addType('geography', 'CrEOF\Spatial\DBAL\Types\GeographyType');
        Type::addType('geographypoint', 'CrEOF\Spatial\DBAL\Types\Geography\PointType');
        Type::addType('geographylinestring', 'CrEOF\Spatial\DBAL\Types\Geography\LineStringType');
        Type::addType('geographypolygon', 'CrEOF\Spatial\DBAL\Types\Geography\PolygonType');
    }

    /**
     * Setup fixtures
     */
    protected function setupEntities()
    {
        $this->_schemaTool->createSchema(
            array(
                // Geometry
                $this->_em->getClassMetadata(self::GEOMETRY_ENTITY),
                $this->_em->getClassMetadata(self::NO_HINT_GEOMETRY_ENTITY),
                $this->_em->getClassMetadata(self::POINT_ENTITY),
                $this->_em->getClassMetadata(self::LINESTRING_ENTITY),
                $this->_em->getClassMetadata(self::POLYGON_ENTITY),

                // Geography
                $this->_em->getClassMetadata(self::GEOGRAPHY_ENTITY)
            )
        );
    }

    /**
     * Setup DQL functions
     */
    protected function setUpFunctions()
    {
        if ($this->getPlatform()->getName() == 'postgresql') {
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
            $this->_em->getConfiguration()->addCustomNumericFunction('st_distance', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistance');
            $this->_em->getConfiguration()->addCustomStringFunction('st_envelope', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STEnvelope');
            $this->_em->getConfiguration()->addCustomStringFunction('st_geomfromtext', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromText');
            $this->_em->getConfiguration()->addCustomNumericFunction('st_length', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLength');
            $this->_em->getConfiguration()->addCustomNumericFunction('st_linecrossingdirection', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineCrossingDirection');
            $this->_em->getConfiguration()->addCustomStringFunction('st_startpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STStartPoint');
            $this->_em->getConfiguration()->addCustomStringFunction('st_summary', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSummary');
        }

        if ($this->getPlatform()->getName() == 'mysql') {
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
    }

    /**
     * Teardown fixtures
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->_sqlLoggerStack->enabled = false;

        $this->tearDownEntities();
        $this->_em->clear();
    }

    /**
     * Teardown entities
     */
    protected function tearDownEntities()
    {
        // Geometry
        $this->conn->executeUpdate('DELETE FROM GeometryEntity');
        $this->conn->executeUpdate('DELETE FROM NoHintGeometryEntity');
        $this->conn->executeUpdate('DELETE FROM PointEntity');
        $this->conn->executeUpdate('DELETE FROM LineStringEntity');
        $this->conn->executeUpdate('DELETE FROM PolygonEntity');

        // Geography
        $this->conn->executeUpdate('DELETE FROM GeographyEntity');
    }

    /**
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return $this->_em->getConnection()->getDatabasePlatform();
    }
}
