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

namespace CrEOF\Spatial\PHP\Types;

use CrEOF\Spatial\Exception\InvalidValueException;

/**
 * Abstract MultiLineString object for MULTILINESTRING spatial types.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
abstract class AbstractMultiLineString extends AbstractGeometry
{
    /**
     * Array of line strings.
     *
     * @var array[]
     */
    protected $lineStrings = [];

    /**
     * AbstractMultiLineString constructor.
     *
     * @param AbstractLineString[]|array[] $rings array of linestring
     * @param int|null                     $srid  Spatial Reference System Identifier
     *
     * @throws InvalidValueException when rings contains an invalid linestring
     */
    public function __construct(array $rings, $srid = null)
    {
        $this->setLineStrings($rings)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a linestring to geometry.
     *
     * @param AbstractLineString|array[] $lineString the line string to add to Geometry
     *
     * @throws InvalidValueException when linestring is not valid
     *
     * @return self
     */
    public function addLineString($lineString)
    {
        $this->lineStrings[] = $this->validateLineStringValue($lineString);

        return $this;
    }

    /**
     * Return linestring at specified offset.
     *
     * @param int $index offset of line string to return. Use -1 to get last linestring.
     *
     * @return AbstractLineString
     */
    public function getLineString($index)
    {
        if (-1 == $index) {
            $index = count($this->lineStrings) - 1;
        }

        $lineStringClass = $this->getNamespace().'\LineString';

        return new $lineStringClass($this->lineStrings[$index], $this->srid);
    }

    /**
     * Line strings getter.
     *
     * @return AbstractLineString[]
     */
    public function getLineStrings()
    {
        $lineStrings = [];

        for ($i = 0; $i < count($this->lineStrings); ++$i) {
            $lineStrings[] = $this->getLineString($i);
        }

        return $lineStrings;
    }

    /**
     * Type getter.
     *
     * @return string MultiLineString
     */
    public function getType()
    {
        return self::MULTILINESTRING;
    }

    /**
     * LineStrings fluent setter.
     *
     * @param AbstractLineString[] $lineStrings array of LineString
     *
     * @throws InvalidValueException when a linestring is not valid
     *
     * @return self
     */
    public function setLineStrings(array $lineStrings)
    {
        $this->lineStrings = $this->validateMultiLineStringValue($lineStrings);

        return $this;
    }

    /**
     * Implements abstract method to convert line strings into an array.
     *
     * @return array[]
     */
    public function toArray()
    {
        return $this->lineStrings;
    }
}
