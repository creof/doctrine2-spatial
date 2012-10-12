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

namespace CrEOF\PHP\Types\Spatial\Geometry;

use CrEOF\Exception\InvalidValueException;
use CrEOF\PHP\Types\Spatial\Geometry;
use CrEOF\PHP\Types\Spatial\Geometry\LineString;
use CrEOF\PHP\Types\Spatial\Geometry\Point;

/**
 * Polygon object for POLYGON geometry type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class Polygon extends Geometry
{
    /**
     * @var LineString[] $polygons
     */
    protected $rings = array();

    /**
     * @param LineString[] $rings
     */
    public function __construct(array $rings)
    {
        $this->setRings($rings);
    }

    /**
     * @param LineString $lineString
     *
     * @return self
     */
    public function addRing(LineString $lineString)
    {
        $this->rings[] = $lineString;

        return $this;
    }

    /**
     * @return LineString[]
     */
    public function getRings()
    {
        return $this->rings;
    }

    /**
     * @param LineString[] $rings
     *
     * @return self
     */
    public function setRings(array $rings)
    {
        $this->validatePolygonValue($rings);

        $this->rings = $rings;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::POLYGON;
    }
}
