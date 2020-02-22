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
 * Abstract Polygon object for POLYGON spatial types.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractPolygon extends AbstractGeometry
{
    /**
     * Polygons are rings.
     *
     * @var array[]
     */
    protected $rings = [];

    /**
     * Abstract polygon constructor.
     *
     * @param AbstractLineString[]|array[] $rings the polygons
     * @param int|null                     $srid  Spatial Reference System Identifier
     *
     * @throws InvalidValueException When a ring is invalid
     */
    public function __construct(array $rings, $srid = null)
    {
        $this->setRings($rings)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a polygon to geometry.
     *
     * @param AbstractLineString|array[] $ring Ring to add to geometry
     *
     * @throws InvalidValueException when a ring is invalid
     *
     * @return self
     */
    public function addRing($ring)
    {
        $this->rings[] = $this->validateRingValue($ring);

        return $this;
    }

    /**
     * Polygon getter.
     *
     * @param int $index index of polygon, use -1 to get last one
     *
     * @return AbstractLineString
     */
    public function getRing($index)
    {
        if (-1 == $index) {
            $index = count($this->rings) - 1;
        }

        $lineStringClass = $this->getNamespace().'\LineString';

        return new $lineStringClass($this->rings[$index], $this->srid);
    }

    /**
     * Rings getter.
     *
     * @return AbstractLineString[]
     */
    public function getRings()
    {
        $rings = [];

        for ($i = 0; $i < count($this->rings); ++$i) {
            $rings[] = $this->getRing($i);
        }

        return $rings;
    }

    /**
     * Type getter.
     *
     * @return string Polygon
     */
    public function getType()
    {
        return self::POLYGON;
    }

    /**
     * Rings fluent setter.
     *
     * @param AbstractLineString[] $rings Rings to set
     *
     * @throws InvalidValueException when a ring is invalid
     *
     * @return self
     */
    public function setRings(array $rings)
    {
        $this->rings = $this->validatePolygonValue($rings);

        return $this;
    }

    /**
     * Converts rings to array.
     *
     * @return array[]
     */
    public function toArray()
    {
        return $this->rings;
    }
}
