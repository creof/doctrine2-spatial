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

        $class = 'CrEOF\Spatial\PHP\Types\Geometry\\' . $value['type'];

        return new $class($value['value'], $value['srid']);
    }

    /**
     * {@inheritdoc}
     */
    public function convertBinaryToPHPValue($sqlExpr)
    {
        $parser = new BinaryParser($sqlExpr);

        $value = $parser->parse();

        $class = 'CrEOF\Spatial\PHP\Types\Geometry\\' . $value['type'];

        return new $class($value['value'], $value['srid']);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value)
    {
        if ($value === null) {
            return $value;
        }

        if ( ! ($value instanceof AbstractGeometry)) {
            throw InvalidValueException::invalidValueNotGeometry();
        }

        return sprintf('%s(%s)', strtoupper($value->getType()), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value)
    {
        if (null === $value) {
            return null;
        }

        if (gettype($value) == 'string' && ord($value) > 31) {
            return $this->convertStringToPHPValue($value);
        }

        return $this->convertBinaryToPHPValue($value);
    }
}
