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

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common test code.
 */
abstract class OrmMockTestCase extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $mockEntityManager;

    /**
     * Setup the mocked entity manager.
     *
     * @throws DBALException when connection is not successful
     * @throws ORMException  when cache is not set
     */
    protected function setUp(): void
    {
        $this->mockEntityManager = $this->getMockEntityManager();
    }

    /**
     * Return the mocked connection.
     *
     * @throws DBALException when connection is not successful
     *
     * @return Connection
     */
    protected function getMockConnection()
    {
        /** @var Driver|MockObject $driver */
        $driver = $this->getMockBuilder('Doctrine\DBAL\Driver\PDOSqlite\Driver')
            ->setMethods(['getDatabasePlatform'])
            ->getMock()
        ;
        $platform = $this->getMockBuilder('Doctrine\DBAL\Platforms\SqlitePlatform')
            ->setMethods(['getName'])
            ->getMock()
        ;

        $platform->method('getName')
            ->willReturn('YourSQL')
        ;
        $driver->method('getDatabasePlatform')
            ->willReturn($platform)
        ;

        return new Connection([], $driver);
    }

    /**
     * Get the mocked entity manager.
     *
     * @throws DBALException When connection is not successful
     * @throws ORMException  won't happen because Metadata cache is set
     *
     * @return EntityManagerInterface a mocked entity manager
     */
    protected function getMockEntityManager()
    {
        if (isset($this->mockEntityManager)) {
            return $this->mockEntityManager;
        }

        $path = [realpath(__DIR__.'/Fixtures')];
        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setProxyDir(__DIR__.'/Proxies');
        $config->setProxyNamespace('CrEOF\Spatial\Tests\Proxies');
        //TODO Warning wrong paramater is provided
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($path, true));

        return EntityManager::create($this->getMockConnection(), $config);
    }
}
