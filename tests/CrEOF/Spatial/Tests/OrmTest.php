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
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Abstract ORM test class
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class OrmTest extends \PHPUnit_Framework_TestCase
{
    const GEOMETRY_ENTITY         = 'CrEOF\Spatial\Tests\Fixtures\GeometryEntity';
    const NO_HINT_GEOMETRY_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\NoHintGeometryEntity';
    const POINT_ENTITY            = 'CrEOF\Spatial\Tests\Fixtures\PointEntity';
    const LINESTRING_ENTITY       = 'CrEOF\Spatial\Tests\Fixtures\LineStringEntity';
    const POLYGON_ENTITY          = 'CrEOF\Spatial\Tests\Fixtures\PolygonEntity';
    const GEOGRAPHY_ENTITY        = 'CrEOF\Spatial\Tests\Fixtures\GeographyEntity';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var bool[]
     */
    protected $usedTypes = array();

    /**
     * @var bool[]
     */
    protected $usedEntities = array();

    /**
     * @var bool[]
     */
    protected $supportedPlatforms = array();

    /**
     * @var bool[]
     */
    protected static $createdEntities = array();

    /**
     * @var bool[]
     */
    protected static $addedTypes = array();

    /**
     * @var Connection
     */
    protected static $connection;

    /**
     * @var array[]
     */
    protected static $entities = array(
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
     * @var string[]
     */
    protected static $types = array(
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
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var DebugStack
     */
    private $sqlLoggerStack;

    /**
     * @throws UnsupportedPlatformException
     */
    public static function setUpBeforeClass()
    {
        static::$connection = static::getConnection();
    }

    /**
     * Creates a connection to the test database, if there is none yet, and
     * creates the necessary tables.
     *
     * @throws UnsupportedPlatformException
     */
    protected function setUp()
    {
        if (count($this->supportedPlatforms) && ! isset($this->supportedPlatforms[$this->getPlatform()->getName()])) {
            $this->markTestSkipped(sprintf('No support for platform %s in test class %s.', $this->getPlatform()->getName(), get_class($this)));
        }

        $this->entityManager = $this->getEntityManager();
        $this->schemaTool    = $this->getSchemaTool();

        if ($GLOBALS['opt_mark_sql']) {
            static::getConnection()->executeQuery(sprintf('SELECT 1 /*%s*//*%s*/', get_class($this), $this->getName()));
        }

        $this->sqlLoggerStack->enabled = true;

        $this->setUpTypes();
        $this->setUpEntities();
        $this->setUpFunctions();
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        if (isset($this->entityManager)) {
            return $this->entityManager;
        }

        $this->sqlLoggerStack          = new DebugStack();
        $this->sqlLoggerStack->enabled = false;

        static::getConnection()->getConfiguration()->setSQLLogger($this->sqlLoggerStack);

        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache);
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('CrEOF\Spatial\Tests\Proxies');
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(array(realpath(__DIR__ . '/Fixtures')), true));

        return EntityManager::create(static::getConnection(), $config);
    }

    /**
     * @return SchemaTool
     */
    protected function getSchemaTool()
    {
        if (isset($this->schemaTool)) {
            return $this->schemaTool;
        }

        return new SchemaTool($this->getEntityManager());
    }

    /**
     * @param string $typeName
     */
    protected function usesType($typeName)
    {
        $this->usedTypes[$typeName] = true;
    }

    /**
     * @param string $platform
     */
    protected function supportsPlatform($platform)
    {
        $this->supportedPlatforms[$platform] = true;
    }

    /**
     * @param string $entityName
     */
    protected function usesEntity($entityName)
    {
        $this->usedEntities[$entityName] = true;

        foreach (static::$entities[$entityName]['types'] as $type) {
            $this->usesType($type);
        }
    }

    /**
     * @return array
     */
    protected function getEntityClasses()
    {
        return array_column(array_intersect_key(static::$entities, static::$createdEntities), 'class');
    }

    /**
     * Add types used by test to DBAL
     */
    protected function setUpTypes()
    {
        foreach (array_keys($this->usedTypes) as $typeName) {
            if (! isset(static::$addedTypes[$typeName])) {
                Type::addType($typeName, static::$types[$typeName]);

                // Since doctrineTypeComments may already be initialized check if added type requires comment
                if (Type::getType($typeName)->requiresSQLCommentHint($this->getPlatform())) {
                    $this->getPlatform()->markDoctrineTypeCommented(Type::getType($typeName));
                }

                static::$addedTypes[$typeName] = true;
            }
        }
    }

    /**
     * Create entities used by tests
     */
    protected function setUpEntities()
    {
        $classes = array();

        foreach (array_keys($this->usedEntities) as $entityName) {
            if (! isset(static::$createdEntities[$entityName])) {
                //TODO: getClassMetadata marked internal in 2.5
                $classes[] = $this->getEntityManager()->getClassMetadata(static::$entities[$entityName]['class']);

                static::$createdEntities[$entityName] = true;
            }
        }

        if ($classes) {
            $this->getSchemaTool()->createSchema($classes);
        }
    }

    /**
     * Setup DQL functions
     */
    protected function setUpFunctions()
    {
        $configuration = $this->getEntityManager()->getConfiguration();

        if ($this->getPlatform()->getName() == 'postgresql') {
            $configuration->addCustomStringFunction('st_asbinary', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsBinary');
            $configuration->addCustomStringFunction('st_astext', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsText');
            $configuration->addCustomNumericFunction('st_area', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STArea');
            $configuration->addCustomStringFunction('st_centroid', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCentroid');
            $configuration->addCustomStringFunction('st_closestpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STClosestPoint');
            $configuration->addCustomNumericFunction('st_contains', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContains');
            $configuration->addCustomNumericFunction('st_containsproperly', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContainsProperly');
            $configuration->addCustomNumericFunction('st_covers', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCovers');
            $configuration->addCustomNumericFunction('st_coveredby', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCoveredBy');
            $configuration->addCustomNumericFunction('st_crosses', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCrosses');
            $configuration->addCustomNumericFunction('st_disjoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDisjoint');
            $configuration->addCustomNumericFunction('st_distance', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistance');
            $configuration->addCustomNumericFunction('st_distance_sphere', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistanceSphere');
            $configuration->addCustomStringFunction('st_envelope', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STEnvelope');
            $configuration->addCustomStringFunction('st_geomfromtext', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromText');
            $configuration->addCustomNumericFunction('st_length', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLength');
            $configuration->addCustomNumericFunction('st_linecrossingdirection', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineCrossingDirection');
            $configuration->addCustomStringFunction('st_startpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STStartPoint');
            $configuration->addCustomStringFunction('st_summary', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSummary');
        }

        if ($this->getPlatform()->getName() == 'mysql') {
            $configuration->addCustomNumericFunction('area', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Area');
            $configuration->addCustomStringFunction('asbinary', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsBinary');
            $configuration->addCustomStringFunction('astext', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsText');
            $configuration->addCustomNumericFunction('contains', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Contains');
            $configuration->addCustomNumericFunction('disjoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Disjoint');
            $configuration->addCustomStringFunction('envelope', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Envelope');
            $configuration->addCustomStringFunction('geomfromtext', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeomFromText');
            $configuration->addCustomNumericFunction('glength', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GLength');
            $configuration->addCustomNumericFunction('mbrcontains', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRContains');
            $configuration->addCustomNumericFunction('mbrdisjoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRDisjoint');
            $configuration->addCustomStringFunction('startpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\StartPoint');
        }
    }

    /**
     * Teardown fixtures
     */
    protected function tearDown()
    {
        $classes = array();

        $this->sqlLoggerStack->enabled = false;

        foreach (array_keys($this->usedEntities) as $entityName) {
            static::getConnection()->executeUpdate(sprintf('DELETE FROM %s', static::$entities[$entityName]['table']));
        }

        $this->getEntityManager()->clear();
    }

    /**
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return static::getConnection()->getDatabasePlatform();
    }

    /**
     * @param \Exception $e
     *
     * @return void
     *
     * @throws \Exception
     */
//    protected function onNotSuccessfulTest(\Exception $e)
//    {
//        if ($e instanceof \PHPUnit_Framework_AssertionFailedError) {
//            throw $e;
//        }
//
//        if (isset($this->sqlLoggerStack->queries) && count($this->sqlLoggerStack->queries)) {
//            $queries = "";
//            for ($i = count($this->sqlLoggerStack->queries)-1; $i > max(count($this->sqlLoggerStack->queries)-25, 0) && isset($this->sqlLoggerStack->queries[$i]); $i--) {
//                $query = $this->sqlLoggerStack->queries[$i];
//                $params = array_map(function ($p) {
//                    if (is_object($p)) {
//                        return get_class($p);
//                    }
//
//                    return "'".$p."'";
//                }, $query['params'] ?: array());
//                $queries .= ($i+1).". SQL: '".$query['sql']."' Params: ".implode(", ", $params).PHP_EOL;
//            }
//
//            $trace = $e->getTrace();
//            $traceMsg = "";
//            foreach ($trace as $part) {
//                if (isset($part['file'])) {
//                    if (strpos($part['file'], "PHPUnit/") !== false) {
//                        // Beginning with PHPUnit files we don't print the trace anymore.
//                        break;
//                    }
//
//                    $traceMsg .= $part['file'].":".$part['line'].PHP_EOL;
//                }
//            }
//
//            $message = "[".get_class($e)."] ".$e->getMessage().PHP_EOL.PHP_EOL."With queries:".PHP_EOL.$queries.PHP_EOL."Trace:".PHP_EOL.$traceMsg;
//
//            throw new \Exception($message, (int)$e->getCode(), $e);
//        }
//        throw $e;
//    }

    /**
     * Using the SQL Logger Stack this method retrieves the current query count executed in this test.
     *
     * @return int
     */
    protected function getCurrentQueryCount()
    {
        return count($this->sqlLoggerStack->queries);
    }

    /**
     * @return Connection
     * @throws UnsupportedPlatformException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected static function getConnection()
    {
        if (isset(static::$connection)) {
            return static::$connection;
        }

        $connection = DriverManager::getConnection(static::getConnectionParameters());

        switch ($connection->getDatabasePlatform()->getName()) {
            case 'postgresql':
                $connection->exec('CREATE EXTENSION postgis');
                break;
            case 'mysql':
                break;
            default:
                throw UnsupportedPlatformException::unsupportedPlatform($connection->getDatabasePlatform()->getName());
        }

        return $connection;
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected static function getConnectionParameters()
    {
        $parameters           = static::getCommonConnectionParameters();
        $parameters['dbname'] = $GLOBALS['db_name'];

        $connection           = DriverManager::getConnection($parameters);
        $dbName               = $connection->getDatabase();

        $connection->close();

        $tmpConnection = DriverManager::getConnection(static::getCommonConnectionParameters());

        $tmpConnection->getSchemaManager()->dropAndCreateDatabase($dbName);
        $tmpConnection->close();

        return $parameters;
    }

    /**
     * @return array
     */
    protected static function getCommonConnectionParameters()
    {
        $connectionParams = array(
            'driver'   => $GLOBALS['db_type'],
            'user'     => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
            'host'     => $GLOBALS['db_host'],
            'dbname'   => null,
            'port'     => $GLOBALS['db_port']
        );

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = $GLOBALS['db_server'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        return $connectionParams;
    }
}
