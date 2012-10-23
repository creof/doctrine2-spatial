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

namespace CrEOF\Spatial\PHP\Types;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\AbstractPoint;

/**
 * Abstract MultiLineString object for MULTILINESTRING spatial types
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractMultiLineString extends AbstractGeometry
{
    /**
     * @var array[] $lineStrings
     */
    protected $lineStrings = array();

    /**
     * @param AbstractLineString[]|array[] $rings
     * @param null|int                     $srid
     */
    public function __construct(array $rings, $srid = null)
    {
        $this->setLineStrings($rings)
            ->setSrid($srid);
    }

    /**
     * @param AbstractLineString|array[] $lineString
     *
     * @return self
     */
    public function addLineString($lineString)
    {
        $this->lineStrings[] = $this->validateLineStringValue($lineString);

        return $this;
    }

    /**
     * @return AbstractLineString[]
     */
    public function getLineStrings()
    {
        $lineStrings = array();

        for ($i = 0; $i < count($this->lineStrings); $i++) {
            $lineStrings[] = $this->getLineString($i);
        }

        return $lineStrings;
    }

    /**
     * @param int $index
     *
     * @return AbstractLineString
     */
    public function getLineString($index)
    {
        if ($index == -1) {
            $index = count($this->lineStrings) - 1;
        }

        $lineStringClass = $this->getNamespace() . '\LineString';

        return new $lineStringClass($this->lineStrings[$index], $this->srid);
    }

    /**
     * @param AbstractLineString[] $lineStrings
     *
     * @return self
     */
    public function setLineStrings(array $lineStrings)
    {
        $this->lineStrings = $this->validateMultiLineStringValue($lineStrings);

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::MULTILINESTRING;
    }

    /**
     * @return array[]
     */
    public function toArray()
    {
        return $this->lineStrings;
    }
}
