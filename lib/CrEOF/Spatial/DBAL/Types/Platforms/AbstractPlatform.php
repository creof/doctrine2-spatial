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

namespace CrEOF\Spatial\DBAL\Types\Platforms;

use CrEOF\Spatial\PHP\Types\AbstractGeometry;
use CrEOF\Spatial\DBAL\Types\StringParser;
use CrEOF\Spatial\DBAL\Types\BinaryParser;
use CrEOF\Spatial\Exception\InvalidValueException;

/**
 * Abstract spatial platform
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertStringToPHPValue($sqlExpr)
    {
        $parser = new StringParser($sqlExpr);

        $value = $parser->parse();

        $class = sprintf('CrEOF\Spatial\PHP\Types\%s\%s', $this->getBaseType(), $value['type']);

        return new $class($value['value'], $value['srid']);
    }

    /**
     * {@inheritdoc}
     */
    public function convertBinaryToPHPValue($sqlExpr)
    {
        if ($sqlExpr[0]=='x') $sqlExpr=substr($sqlExpr, 1); // some versions of PostgreSQL/PostGIS prefix hex values with an x
        $parser = new BinaryParser($sqlExpr);

        $value = $parser->parse();

        $class = sprintf('CrEOF\Spatial\PHP\Types\%s\%s', $this->getBaseType(), $this->getUnqualifiedClassForGeometryType($value['type']));

        return new $class($value['value'], $value['srid']);
    }

    /**
     * Get the unqualified classname for a Geometry type
     *
     * @see \CrEOF\Spatial\PHP\Types\AbstractGeometry
     * @link http://php.net/manual/en/reflectionclass.getconstants.php
     * @param string $type The Geometry type, for example: 'MULTILINESTRING'
     * @return string The unqualified class name, for example: 'MultiLineString'
     * @throws \Exception
     */
    public function getUnqualifiedClassForGeometryType($type)
    {
        $r = new \ReflectionClass('\CrEOF\Spatial\PHP\Types\AbstractGeometry');

        //Iterate over the associative array label -> value
        foreach ($r->getConstants() as $label => $value) {
            if ($label == $type) {
                return $value;
            }
        }

        throw new \Exception("Unsupported Geometry type used: '$type'");
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue(AbstractGeometry $value)
    {
        return sprintf('%s(%s)', strtoupper($value->getType()), $value);
    }
}
