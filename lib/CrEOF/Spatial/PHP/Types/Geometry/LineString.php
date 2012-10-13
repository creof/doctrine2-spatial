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

namespace CrEOF\Spatial\PHP\Types\Geometry;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geometry;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

/**
 * LineString object for LINESTRING geometry type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class LineString extends Geometry
{
    /**
     * @var Point[] $points
     */
    protected $points = array();

    /**
     * @param Point[] $points
     */
    public function __construct(array $points)
    {
        $this->setPoints($points);
    }

    /**
     * @param Point $point
     *
     * @return self
     */
    public function addPoint(Point $point)
    {
        $this->points[] = $point;

        return $this;
    }

    /**
     * @return Point[]
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param int $index
     *
     * @return Point
     */
    public function getPoint($index)
    {
        if ($index == -1) {
            return $this->points[count($this->points) - 1];
        }

        return $this->points[$index];
    }

    /**
     * @param Point[] $points
     *
     * @return self
     */
    public function setPoints(array $points)
    {
        $this->validateLineStringValue($points);

        $this->points = $points;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::LINESTRING;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        if ($this->getPoint(0) != $this->getPoint(-1)) {
            return false;
        }

        return true;
    }

}
