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
    const GEOMETRY_ENTITY         = 'CrEOF\Spatial\Tests\Fixtures\GeometryEntity';
    const NO_HINT_GEOMETRY_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\NoHintGeometryEntity';
    const POINT_ENTITY            = 'CrEOF\Spatial\Tests\Fixtures\PointEntity';
    const LINESTRING_ENTITY       = 'CrEOF\Spatial\Tests\Fixtures\LineStringEntity';
    const POLYGON_ENTITY          = 'CrEOF\Spatial\Tests\Fixtures\PolygonEntity';
    const GEOGRAPHY_ENTITY        = 'CrEOF\Spatial\Tests\Fixtures\GeographyEntity';

    /**
     * @var bool
     */
    protected static $_setup = false;

    /**
     * @var bool
     */
    protected static $_platformSetup = false;

    /**
     * @var Connection
     */
    protected static $_sharedConn;

    /**
     * @var array
     */
    protected static $_entities = array(
        'geometry' => array(
            'class' => self::GEOMETRY_ENTITY,
            'types' => array('geometry'),
            'table' => 'GeometryEntity'
        ),
        'no_hint_geometry' => array(
            'class' => self::NO_HINT_GEOMETRY_ENTITY,
            'types' => array('geometry'),
            'table' => 'NoHintGeometryEntity'
        ),
        'point' => array(
            'class' => self::POINT_ENTITY,
            'types' => array('point'),
            'table' => 'PointEntity'
        ),
        'linestring' => array(
            'class' => self::LINESTRING_ENTITY,
            'types' => array('linestring'),
            'table' => 'LineStringEntity'
        ),
        'polygon' => array(
            'class' => self::POLYGON_ENTITY,
            'types' => array('polygon'),
            'table' => 'PolygonEntity'
        ),
        'geography' => array(
            'class' => self::GEOGRAPHY_ENTITY,
            'types' => array('geography'),
            'table' => 'GeographyEntity'
        )
    );

    /**
     * @var array
     */
    protected static $_types = array(
        'geometry'      => 'CrEOF\Spatial\DBAL\Types\GeometryType',
        'point'         => 'CrEOF\Spatial\DBAL\Types\Geometry\PointType',
        'linestring'    => 'CrEOF\Spatial\DBAL\Types\Geometry\LineStringType',
        'polygon'       => 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType',
        'geography'     => 'CrEOF\Spatial\DBAL\Types\GeographyType',
        'geopoint'      => 'CrEOF\Spatial\DBAL\Types\Geography\PointType',
        'geolinestring' => 'CrEOF\Spatial\DBAL\Types\Geography\LineStringType',
        'geopolygon'    => 'CrEOF\Spatial\DBAL\Types\Geography\PolygonType'
    );

    /**
     * @var array
     */
    protected static $_entitiesCreated = array();

    /**
     * @var array
     */
    protected static $_typesAdded = array();

    /**
     * @var array
     */
    protected $_usedEntities = array();

    /**
     * @var array
     */
    protected $_usedTypes = array();

    /**
     * @param string $typeName
     */
    protected function useType($typeName)
    {
        $this->_usedTypes[$typeName] = true;
    }

    /**
     * @param string $entityName
     */
    protected function useEntity($entityName)
    {
        $this->_usedEntities[$entityName] = true;

        foreach (static::$_entities[$entityName]['types'] as $type) {
            $this->useType($type);
        }
    }

    protected function getEntityClasses()
    {
        return array_column(array_intersect_key(static::$_entities, static::$_entitiesCreated), 'class');
    }

    /**
     * @throws UnsupportedPlatformException
     */
    protected function setUp()
    {
        parent::setUp();

        if ( ! static::$_platformSetup) {
            static::$_platformSetup = true;

            $this->setupPlatform();
        }

        $this->setUpTypes();
        $this->setUpEntities();
        $this->setupFunctions();
    }

    /**
     * Add types used by test to DBAL
     */
    protected function setUpTypes()
    {
        foreach ($this->_usedTypes as $typeName => $bool) {
            if ( ! isset(static::$_typesAdded[$typeName])) {
                Type::addType($typeName, static::$_types[$typeName]);

                // Since doctrineTypeComments may already be initialized check if added type requires comment
                if (Type::getType($typeName)->requiresSQLCommentHint($this->getPlatform())) {
                    $this->getPlatform()->markDoctrineTypeCommented($typeName);
                }

                static::$_typesAdded[$typeName] = true;
            }
        }
    }

    /**
     * Create entities used by tests
     */
    protected function setUpEntities()
    {
        $classes = array();

        foreach ($this->_usedEntities as $entityName => $bool) {
            if ( ! isset(static::$_entitiesCreated[$entityName])) {
                $classes[] = $this->_em->getClassMetadata(static::$_entities[$entityName]['class']);

                static::$_entitiesCreated[$entityName] = true;
            }
        }

        if ($classes) {
            $this->_schemaTool->createSchema($classes);
        }
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
                static::$_sharedConn->exec('CREATE EXTENSION postgis');
                break;
            case 'mysql':
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($this->getPlatform()->getName());
                break;
        }
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
        $conn = static::$_sharedConn;

        $this->_sqlLoggerStack->enabled = false;

        foreach ($this->_usedEntities as $entityName => $bool) {
            $conn->executeUpdate(sprintf('DELETE FROM %s', static::$_entities[$entityName]['table']));
        }

        $this->_em->clear();
    }

    /**
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return static::$_sharedConn->getDatabasePlatform();
    }
}
