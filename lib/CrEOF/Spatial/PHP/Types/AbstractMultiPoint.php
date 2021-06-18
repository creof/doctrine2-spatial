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
 * Abstract MultiPoint object for MULTIPOINT spatial types.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
abstract class AbstractMultiPoint extends AbstractGeometry
{
    /**
     * @var array[]
     */
    protected $points = [];

    /**
     * Abstract multipoint constructor.
     *
     * @param AbstractPoint[]|array[] $points array of point
     * @param int|null                $srid   Spatial Reference System Identifier
     *
     * @throws InvalidValueException when a point is not valid
     */
    public function __construct(array $points, $srid = null)
    {
        $this->setPoints($points)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a point to geometry.
     *
     * @param AbstractPoint|array $point Point to add to geometry
     *
     * @throws InvalidValueException when the point is not valid
     *
     * @return self
     */
    public function addPoint($point)
    {
        $this->points[] = $this->validatePointValue($point);

        return $this;
    }

    /**
     * Point getter.
     *
     * @param int $index index of the point to retrieve. -1 to get last point.
     *
     * @return AbstractPoint
     */
    public function getPoint($index)
    {
        switch ($index) {
            case -1:
                $point = $this->points[count($this->points) - 1];
                break;
            default:
                $point = $this->points[$index];
                break;
        }

        $pointClass = $this->getNamespace().'\Point';

        return new $pointClass($point[0], $point[1], $this->srid);
    }

    /**
     * Points getter.
     *
     * @return AbstractPoint[]
     */
    public function getPoints()
    {
        $points = [];

        for ($i = 0; $i < count($this->points); ++$i) {
            $points[] = $this->getPoint($i);
        }

        return $points;
    }

    /**
     * Type getter.
     *
     * @return string Multipoint
     */
    public function getType()
    {
        return self::MULTIPOINT;
    }

    /**
     * Points fluent setter.
     *
     * @param AbstractPoint[]|array[] $points the points
     *
     * @throws InvalidValueException when a point is invalid
     *
     * @return self
     */
    public function setPoints($points)
    {
        $this->points = $this->validateMultiPointValue($points);

        return $this;
    }

    /**
     * Convert multipoint to array.
     *
     * @return array[]
     */
    public function toArray()
    {
        return $this->points;
    }
}
