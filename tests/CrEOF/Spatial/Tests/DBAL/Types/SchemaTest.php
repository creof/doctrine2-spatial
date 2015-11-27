<?php
/**
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

namespace CrEOF\Spatial\Tests\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use CrEOF\Spatial\Tests\OrmTestCase;

/**
 * Doctrine schema related tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class SchemaTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->usesEntity(self::MULTIPOLYGON_ENTITY);

        if ($this->getPlatform()->getName() === 'postgresql') {
            $this->usesEntity(self::GEOGRAPHY_ENTITY);
            $this->usesEntity(self::GEO_POINT_SRID_ENTITY);
            $this->usesEntity(self::GEO_LINESTRING_ENTITY);
            $this->usesEntity(self::GEO_POLYGON_ENTITY);
        }

        parent::setUp();
    }

    public function testDoctrineTypeMapping()
    {
        $platform = $this->getPlatform();

        foreach ($this->getAllClassMetadata() as $metadata) {
            foreach ($metadata->getFieldNames() as $fieldName) {
                $doctrineType  = $metadata->getTypeOfField($fieldName);
                $type          = Type::getType($doctrineType);
                $databaseTypes =  $type->getMappedDatabaseTypes($platform);

                foreach ($databaseTypes as $databaseType) {
                    $typeMapping = $this->getPlatform()->getDoctrineTypeMapping($databaseType);

                    $this->assertEquals($doctrineType, $typeMapping);
                }
            }
        }
    }

    public function testSchemaReverseMapping()
    {
        $result = $this->getSchemaTool()->getUpdateSchemaSql($this->getAllClassMetadata(), true);

        $this->assertCount(0, $result);
    }

    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadata[]
     */
    private function getAllClassMetadata()
    {
        $metadata = array();

        foreach (array_keys($this->getUsedEntityClasses()) as $entityClass) {
            $metadata[] = $this->getEntityManager()->getClassMetadata($entityClass);
        }

        return $metadata;
    }
}
