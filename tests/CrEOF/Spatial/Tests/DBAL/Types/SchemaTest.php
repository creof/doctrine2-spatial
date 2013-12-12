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

namespace CrEOF\Spatial\Tests\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use CrEOF\Spatial\Tests\OrmTest;

/**
 * Doctrine schema related tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group result_processing
 */
class SchemaTest extends OrmTest
{
    public function testDoctrineTypeMapping()
    {
        foreach ($this->getAllClassMetadata() as $metadata) {
            foreach ($metadata->getFieldNames() as $fieldName) {
                $fieldType = $metadata->getTypeOfField($fieldName);

                // Throws exception if mapping does not exist
                $typeMapping = $this->getPlatform()->getDoctrineTypeMapping($fieldType);
            }
        }
    }

    public function testSchemaReverseMapping()
    {
        $result = $this->_schemaTool->getUpdateSchemaSql($this->getAllClassMetadata(), true);

        $this->assertCount(0, $result);
    }

    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadata[]
     */
    private function getAllClassMetadata()
    {
        $metadata = array();

        foreach ($this->getEntityClasses() as $entityClass) {
            $metadata[] = $this->_em->getClassMetadata($entityClass);
        }

        return $metadata;
    }
}
