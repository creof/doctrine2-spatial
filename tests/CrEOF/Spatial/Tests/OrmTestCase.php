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

namespace CrEOF\Spatial\Tests;

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\ORM\Query\AST\Functions\MySql\SpDistance;
use CrEOF\Spatial\ORM\Query\AST\Functions\MySql\SpBuffer;
use CrEOF\Spatial\ORM\Query\AST\Functions\MySql\SpBufferStrategy;
use CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\ScGeographyFromText;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StArea;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StAsBinary;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StAsText;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StBoundary;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StBuffer;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StContains;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StConvexHull;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StCrosses;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StDifference;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StDimension;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StDisjoint;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StDistance;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StEndPoint;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StEnvelope;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StEquals;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StGeometryType;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StGeomFromText;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StIntersection;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StIntersects;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StIsEmpty;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StIsRing;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StIsSimple;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StLength;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StOverlaps;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StRelate;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StSrid;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StStartPoint;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StSymDifference;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StTouches;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StUnion;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StWithin;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StX;
use CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StY;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * Abstract ORM test class.
 */
abstract class OrmTestCase extends TestCase
{
    //Fixtures and entities
    public const GEO_LINESTRING_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\GeoLineStringEntity';
    public const GEO_POINT_SRID_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\GeoPointSridEntity';
    public const GEO_POLYGON_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\GeoPolygonEntity';
    public const GEOGRAPHY_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\GeographyEntity';
    public const GEOMETRY_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\GeometryEntity';
    public const LINESTRING_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\LineStringEntity';
    public const MULTIPOLYGON_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\MultiPolygonEntity';
    public const NO_HINT_GEOMETRY_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\NoHintGeometryEntity';
    public const POINT_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\PointEntity';
    public const POLYGON_ENTITY = 'CrEOF\Spatial\Tests\Fixtures\PolygonEntity';

    /**
     * @var bool[]
     */
    protected static $addedTypes = [];

    /**
     * @var Connection
     */
    protected static $connection;

    /**
     * @var bool[]
     */
    protected static $createdEntities = [];

    /**
     * @var array[]
     */
    protected static $entities = [
        self::GEOMETRY_ENTITY => [
            'types' => ['geometry'],
            'table' => 'GeometryEntity',
        ],
        self::NO_HINT_GEOMETRY_ENTITY => [
            'types' => ['geometry'],
            'table' => 'NoHintGeometryEntity',
        ],
        self::POINT_ENTITY => [
            'types' => ['point'],
            'table' => 'PointEntity',
        ],
        self::LINESTRING_ENTITY => [
            'types' => ['linestring'],
            'table' => 'LineStringEntity',
        ],
        self::POLYGON_ENTITY => [
            'types' => ['polygon'],
            'table' => 'PolygonEntity',
        ],
        self::MULTIPOLYGON_ENTITY => [
            'types' => ['multipolygon'],
            'table' => 'MultiPolygonEntity',
        ],
        self::GEOGRAPHY_ENTITY => [
            'types' => ['geography'],
            'table' => 'GeographyEntity',
        ],
        self::GEO_POINT_SRID_ENTITY => [
            'types' => ['geopoint'],
            'table' => 'GeoPointSridEntity',
        ],
        self::GEO_LINESTRING_ENTITY => [
            'types' => ['geolinestring'],
            'table' => 'GeoLineStringEntity',
        ],
        self::GEO_POLYGON_ENTITY => [
            'types' => ['geopolygon'],
            'table' => 'GeoPolygonEntity',
        ],
    ];

    /**
     * @var string[]
     */
    protected static $types = [
        'geometry' => 'CrEOF\Spatial\DBAL\Types\GeometryType',
        'point' => 'CrEOF\Spatial\DBAL\Types\Geometry\PointType',
        'linestring' => 'CrEOF\Spatial\DBAL\Types\Geometry\LineStringType',
        'polygon' => 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType',
        'multipolygon' => 'CrEOF\Spatial\DBAL\Types\Geometry\MultiPolygonType',
        'geography' => 'CrEOF\Spatial\DBAL\Types\GeographyType',
        'geopoint' => 'CrEOF\Spatial\DBAL\Types\Geography\PointType',
        'geolinestring' => 'CrEOF\Spatial\DBAL\Types\Geography\LineStringType',
        'geopolygon' => 'CrEOF\Spatial\DBAL\Types\Geography\PolygonType',
    ];

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var bool[]
     */
    protected $supportedPlatforms = [];

    /**
     * @var bool[]
     */
    protected $usedEntities = [];

    /**
     * @var bool[]
     */
    protected $usedTypes = [];

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var DebugStack
     */
    private $sqlLoggerStack;

    /**
     * Setup connection before class creation.
     *
     * @throws UnsupportedPlatformException this happen when platform is not mysql or postgresql
     * @throws DBALException                when connection is not successful
     */
    public static function setUpBeforeClass(): void
    {
        static::$connection = static::getConnection();
    }

    /**
     * Creates a connection to the test database, if there is none yet, and creates the necessary tables.
     *
     * @throws UnsupportedPlatformException this should not happen
     * @throws DBALException                this can happen when database or credentials are not set
     * @throws ORMException                 ORM Exception
     */
    protected function setUp(): void
    {
        if (count($this->supportedPlatforms) && !isset($this->supportedPlatforms[$this->getPlatform()->getName()])) {
            static::markTestSkipped(sprintf(
                'No support for platform %s in test class %s.',
                $this->getPlatform()->getName(),
                get_class($this)
            ));
        }

        $this->entityManager = $this->getEntityManager();
        $this->schemaTool = $this->getSchemaTool();

        if ($GLOBALS['opt_mark_sql']) {
            static::getConnection()->executeQuery(sprintf('SELECT 1 /*%s*//*%s*/', get_class($this), $this->getName()));
        }

        $this->sqlLoggerStack->enabled = $GLOBALS['opt_use_debug_stack'];

        $this->setUpTypes();
        $this->setUpEntities();
        $this->setUpFunctions();
    }

    /**
     * Teardown fixtures.
     *
     * @throws UnsupportedPlatformException this should not happen
     * @throws DBALException                this can happen when database or credentials are not set
     * @throws ORMException                 ORM Exception
     * @throws MappingException             Mapping exception when clear fails
     */
    protected function tearDown(): void
    {
        $this->sqlLoggerStack->enabled = false;

        foreach (array_keys($this->usedEntities) as $entityName) {
            static::getConnection()->executeUpdate(sprintf(
                'DELETE FROM %s',
                static::$entities[$entityName]['table']
            ));
        }

        $this->getEntityManager()->clear();
    }

    /**
     * Return common connection parameters.
     *
     * @return array
     */
    protected static function getCommonConnectionParameters()
    {
        $connectionParams = [
            'driver' => $GLOBALS['db_type'],
            'user' => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
            'host' => $GLOBALS['db_host'],
            'dbname' => null,
            'port' => $GLOBALS['db_port'],
        ];

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = $GLOBALS['db_server'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        if (isset($GLOBALS['db_version'])) {
            $connectionParams['driverOptions']['server_version'] = (string) $GLOBALS['db_version'];
        }
        return $connectionParams;
    }

    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
    // phpcs miss the DBALException

    /**
     * Establish the connection if it is not already done, then returns it.
     *
     * @throws DBALException                when connection is not successful
     * @throws UnsupportedPlatformException when platform is unsupported
     *
     * @return Connection
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
                throw new UnsupportedPlatformException(sprintf(
                    'DBAL platform "%s" is not currently supported.',
                    $connection->getDatabasePlatform()->getName()
                ));
        }

        return $connection;
    }

    // phpcs:enable

    /**
     * Return connection parameters.
     *
     * @throws DBALException when connection is not successful
     *
     * @return array
     */
    protected static function getConnectionParameters()
    {
        $parameters = static::getCommonConnectionParameters();
        $parameters['dbname'] = $GLOBALS['db_name'];

        $connection = DriverManager::getConnection($parameters);
        $dbName = $connection->getDatabase();

        $connection->close();

        $tmpConnection = DriverManager::getConnection(static::getCommonConnectionParameters());

        $tmpConnection->getSchemaManager()->dropAndCreateDatabase($dbName);
        $tmpConnection->close();

        return $parameters;
    }

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
     * Return the entity manager.
     *
     * @throws DBALException                when connection is not successful
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        if (isset($this->entityManager)) {
            return $this->entityManager;
        }

        $this->sqlLoggerStack = new DebugStack();
        $this->sqlLoggerStack->enabled = false;

        static::getConnection()->getConfiguration()->setSQLLogger($this->sqlLoggerStack);

        $realPaths = [realpath(__DIR__.'/Fixtures')];
        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setProxyDir(__DIR__.'/Proxies');
        $config->setProxyNamespace('CrEOF\Spatial\Tests\Proxies');
        //TODO WARNING: a non-expected parameter is provided.
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($realPaths, true));

        return EntityManager::create(static::getConnection(), $config);
    }

    /**
     * Get platform.
     *
     * @throws DBALException                this can happen when database or credentials are not set
     * @throws UnsupportedPlatformException this should not happen
     *
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return static::getConnection()->getDatabasePlatform();
    }

    /**
     * Return the schema tool.
     *
     * @throws DBALException                this can happen when database or credentials are not set
     * @throws ORMException                 this can happen when database or credentials are not set
     * @throws UnsupportedPlatformException this should not happen
     *
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
     * Return the static created entity classes.
     *
     * @return array
     */
    protected function getUsedEntityClasses()
    {
        return static::$createdEntities;
    }

    /**
     * On not successful test.
     *
     * @param Throwable $throwable the exception
     *
     * @throws Exception the exception provided by parameter
     */
    protected function onNotSuccessfulTest(Throwable $throwable): void
    {
        if (!$GLOBALS['opt_use_debug_stack'] || $throwable instanceof AssertionFailedError) {
            throw $throwable;
        }

        if (isset($this->sqlLoggerStack->queries) && count($this->sqlLoggerStack->queries)) {
            $queries = '';
            $count = count($this->sqlLoggerStack->queries) - 1;
            $max = max(count($this->sqlLoggerStack->queries) - 25, 0);

            for ($i = $count; $i > $max && isset($this->sqlLoggerStack->queries[$i]); --$i) {
                $query = $this->sqlLoggerStack->queries[$i];
                $params = array_map(function ($param) {
                    if (is_object($param)) {
                        return get_class($param);
                    }

                    return sprintf("'%s'", $param);
                }, $query['params'] ?: []);

                $queries .= sprintf(
                    "%2d. SQL: '%s' Params: %s\n",
                    $i,
                    $query['sql'],
                    implode(', ', $params)
                );
            }

            $trace = $throwable->getTrace();
            $traceMsg = '';

            foreach ($trace as $part) {
                if (isset($part['file'])) {
                    if (false !== mb_strpos($part['file'], 'PHPUnit/')) {
                        // Beginning with PHPUnit files we don't print the trace anymore.
                        break;
                    }

                    $traceMsg .= sprintf("%s:%s\n", $part['file'], $part['line']);
                }
            }

            $message = sprintf("[%s] %s\n\n", get_class($throwable), $throwable->getMessage());
            $message .= sprintf("With queries:\n%s\nTrace:\n%s", $queries, $traceMsg);

            throw new Exception($message, (int) $throwable->getCode(), $throwable);
        }

        throw $throwable;
    }

    /**
     * Create entities used by tests.
     *
     * @throws DBALException                when connection is not successful
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws ToolsException               when schema cannot be created
     */
    protected function setUpEntities()
    {
        $classes = [];

        foreach (array_keys($this->usedEntities) as $entityClass) {
            if (!isset(static::$createdEntities[$entityClass])) {
                static::$createdEntities[$entityClass] = true;
                $classes[] = $this->getEntityManager()->getClassMetadata($entityClass);
            }
        }

        if ($classes) {
            $this->getSchemaTool()->createSchema($classes);
        }
    }

    /**
     * Setup DQL functions.
     *
     * @throws DBALException                when connection is not successful
     * @throws ORMException                 when
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUpFunctions()
    {
        $configuration = $this->getEntityManager()->getConfiguration();

        $configuration->addCustomNumericFunction('ST_Area', StArea::class);
        $configuration->addCustomStringFunction('ST_AsBinary', StAsBinary::class);
        $configuration->addCustomStringFunction('ST_AsText', StAsText::class);
        if ($this->getPlatform()->getName() !== 'mysql') {
            //This function is not implemented into mysql
            $configuration->addCustomStringFunction('ST_Boundary', StBoundary::class);
            //This function is implemented into mysql, but the third parameter does not respect OGC standards
            $configuration->addCustomNumericFunction('ST_Buffer', StBuffer::class);
        }

        $configuration->addCustomNumericFunction('ST_Contains', StContains::class);
        $configuration->addCustomStringFunction('ST_ConvexHull', StConvexHull::class);
        $configuration->addCustomNumericFunction('ST_Crosses', StCrosses::class);
        $configuration->addCustomStringFunction('ST_Difference', StDifference::class);
        $configuration->addCustomNumericFunction('ST_Dimension', StDimension::class);
        $configuration->addCustomNumericFunction('ST_Disjoint', StDisjoint::class);
        $configuration->addCustomNumericFunction('ST_Distance', StDistance::class);
        $configuration->addCustomNumericFunction('ST_Equals', StEquals::class);
        $configuration->addCustomNumericFunction('ST_Intersects', StIntersects::class);
        $configuration->addCustomStringFunction('ST_Intersection', StIntersection::class);
        $configuration->addCustomNumericFunction('ST_IsEmpty', StIsEmpty::class);
        if ($this->getPlatform()->getName() !== 'mysql') {
            //This function is not implemented into mysql
            $configuration->addCustomNumericFunction('ST_IsRing', StIsRing::class);
        }

        $configuration->addCustomNumericFunction('ST_IsSimple', StIsSimple::class);
        $configuration->addCustomStringFunction('ST_EndPoint', StEndPoint::class);
        $configuration->addCustomStringFunction('ST_Envelope', StEnvelope::class);
        $configuration->addCustomStringFunction('ST_GeometryType', StGeometryType::class);
        $configuration->addCustomStringFunction('ST_GeomFromText', StGeomFromText::class);
        $configuration->addCustomNumericFunction('ST_Length', StLength::class);
        $configuration->addCustomStringFunction('ST_Overlaps', StOverlaps::class);
        $configuration->addCustomStringFunction('ST_SymDifference', StSymDifference::class);
        $configuration->addCustomStringFunction('ST_Union', StUnion::class);
        if ($this->getPlatform()->getName() !== 'mysql') {
            $configuration->addCustomStringFunction('ST_Relate', StRelate::class);
        }

        $configuration->addCustomNumericFunction('ST_SRID', StSrid::class);
        $configuration->addCustomNumericFunction('ST_Touches', StTouches::class);
        $configuration->addCustomNumericFunction('ST_Within', StWithin::class);
        $configuration->addCustomNumericFunction('ST_StartPoint', StStartPoint::class);
        $configuration->addCustomNumericFunction('ST_X', StX::class);
        $configuration->addCustomNumericFunction('ST_Y', StY::class);

        if ('postgresql' === $this->getPlatformAndVersion()) {
            //Generic function (PostgreSQL function compatible with the OGC Standard)
            //Specific functions of PostgreSQL server
            $configuration->addCustomStringFunction('SC_GeographyFromText', ScGeographyFromText::class);

            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            $configuration->addCustomStringFunction('geometry', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\Geometry');
            $configuration->addCustomStringFunction('st_centroid', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCentroid');
            $configuration->addCustomStringFunction('st_closestpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STClosestPoint');
            $configuration->addCustomStringFunction('st_collect', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCollect');
            $configuration->addCustomNumericFunction('st_containsproperly', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContainsProperly');
            $configuration->addCustomNumericFunction('st_covers', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCovers');
            $configuration->addCustomNumericFunction('st_coveredby', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCoveredBy');
            $configuration->addCustomNumericFunction('st_distance_sphere', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistanceSphere');
            $configuration->addCustomStringFunction('st_geomfromewkt', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromEWKT');
            $configuration->addCustomNumericFunction('st_linecrossingdirection', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineCrossingDirection');
            $configuration->addCustomStringFunction('st_makeenvelope', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STMakeEnvelope');
            $configuration->addCustomStringFunction('st_snaptogrid', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSnapToGrid');
            $configuration->addCustomStringFunction('st_summary', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSummary');
            // phpcs:enable
        }

        if ('mysql' === $this->getPlatform()->getName()) {
            $configuration->addCustomNumericFunction('Mysql_Distance', SpDistance::class);
            $configuration->addCustomNumericFunction('Mysql_Buffer', SpBuffer::class);
            $configuration->addCustomNumericFunction('Mysql_BufferStrategy', SpBufferStrategy::class);
        }

        if ('mysql5' === $this->getPlatformAndVersion()) {
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
//            $configuration->addCustomNumericFunction('mbrcontains', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRContains');
//            $configuration->addCustomNumericFunction('mbrdisjoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRDisjoint');
//            $configuration->addCustomStringFunction('startpoint', 'CrEOF\Spatial\ORM\Query\AST\Functions\MySql\StartPoint');
            // phpcs:enable
        }
    }

    /**
     * Add types used by test to DBAL.
     *
     * @throws DBALException                when credential or connection failed
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUpTypes()
    {
        foreach (array_keys($this->usedTypes) as $typeName) {
            if (!isset(static::$addedTypes[$typeName]) && !Type::hasType($typeName)) {
                Type::addType($typeName, static::$types[$typeName]);

                $type = Type::getType($typeName);

                // Since doctrineTypeComments may already be initialized check if added type requires comment
                $platform = $this->getPlatform();
                if ($type->requiresSQLCommentHint($platform) && !$platform->isCommentedDoctrineType($type)) {
                    $this->getPlatform()->markDoctrineTypeCommented(Type::getType($typeName));
                }

                static::$addedTypes[$typeName] = true;
            }
        }
    }

    /**
     * Set the supported platforms.
     *
     * @param string $platform the platform to support
     */
    protected function supportsPlatform($platform)
    {
        $this->supportedPlatforms[$platform] = true;
    }

    /**
     * Declare the used entity class to initialized them (and delete its content before the test).
     *
     * @param string $entityClass the entity class
     */
    protected function usesEntity($entityClass)
    {
        $this->usedEntities[$entityClass] = true;

        foreach (static::$entities[$entityClass]['types'] as $type) {
            $this->usesType($type);
        }
    }

    /**
     * Set the type used.
     *
     * @param string $typeName the type name
     */
    protected function usesType($typeName)
    {
        $this->usedTypes[$typeName] = true;
    }

    /**
     * Return the platform completed by the version number of the server for mysql.
     *
     * @throws DBALException                when connection failed
     * @throws UnsupportedPlatformException when platform is not supported
     */
    protected function getPlatformAndVersion(): string
    {
        if ($this->getPlatform() instanceof MySQL80Platform) {
            return 'mysql8';
        }

        if ($this->getPlatform() instanceof MySQL57Platform) {
            return 'mysql5';
        }


        return $this->getPlatform()->getName();
    }

    /**
     * Assert empty geometry.
     * MySQL5 does not return the standard answer, but this bug was solved in MySQL8.
     * So test for an empty geometry is a little more complex than to compare two strings.
     *
     * @param mixed                 $value    Value to test
     * @param AbstractPlatform|null $platform the platform
     */
    protected static function assertEmptyGeometry($value, AbstractPlatform $platform = null): void
    {
        $expected = 'GEOMETRYCOLLECTION EMPTY';
        if ($platform instanceof MySQL57Platform && !$platform instanceof MySQL80Platform) {
            //MySQL5 does not return the standard answer
            //This bug was solved in MySQL8
            $expected = 'GEOMETRYCOLLECTION()';
        }
        self::assertSame($expected, $value);
    }

    /**
     * Assert empty geometry.
     * MySQL5 does not return the standard answer, but this bug was solved in MySQL8.
     * So test for an empty geometry is a little more complex than to compare two strings.
     *
     * @param mixed                 $value    Value to test
     * @param AbstractPlatform|null $platform the platform
     */
    protected static function assertBigPolygon($value, AbstractPlatform $platform = null): void
    {
        switch ($platform->getName()) {
            case 'mysql':
                //MySQL does not respect creation order of points composing a Polygon.
                static::assertSame('POLYGON((0 10,0 0,10 0,10 10,0 10))', $value);
                break;
            case 'postgresl':
            default:
                //Here is the good result.
                // A linestring minus another crossing linestring returns initial linestring splited
                static::assertSame('POLYGON((0 0,0 10,10 10,10 0,0 0))', $value);
        }
    }
}
