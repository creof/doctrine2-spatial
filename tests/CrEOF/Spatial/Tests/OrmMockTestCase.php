<?php

namespace CrEOF\Spatial\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

/**
 * Common test code
 */
abstract class OrmMockTestCase extends \PHPUnit_Framework_TestCase
{
    protected $mockEntityManager;

    protected function setUp()
    {
        $this->mockEntityManager = $this->getMockEntityManager();
    }

    protected function getMockConnection()
    {
        $driver   = $this->getMockBuilder('Doctrine\DBAL\Driver\PDOSqlite\Driver')
                        ->setMethods(array('getDatabasePlatform'))
                        ->getMock();
        $platform = $this->getMockBuilder('Doctrine\DBAL\Platforms\SqlitePlatform')
                        ->setMethods(array('getName'))
                        ->getMock();

        $platform->method('getName')
            ->willReturn('YourSQL');
        $driver->method('getDatabasePlatform')
            ->willReturn($platform);

        $connection = new Connection(array(), $driver);

        return $connection;
    }

    /**
     * @return EntityManager
     */
    protected function getMockEntityManager()
    {
        if (isset($this->mockEntityManager)) {
            return $this->mockEntityManager;
        }

        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache);
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('CrEOF\Spatial\Tests\Proxies');
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(array(realpath(__DIR__ . '/Fixtures')), true));

        return EntityManager::create($this->getMockConnection(), $config);
    }
}
