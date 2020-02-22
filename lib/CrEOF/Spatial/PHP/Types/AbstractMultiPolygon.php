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
abstract class AbstractMultiPolygon extends AbstractGeometry
{
    /**
     * @var array[]
     */
    protected $polygons = [];

    /**
     * AbstractMultiPolygon constructor.
     *
     * @param AbstractPolygon[]|array[] $polygons Polygons
     * @param int|null                  $srid     Spatial Reference System Identifier
     *
     * @throws InvalidValueException when a polygon is invalid
     */
    public function __construct(array $polygons, $srid = null)
    {
        $this->setPolygons($polygons)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a polygon to geometry.
     *
     * @param AbstractPolygon|array[] $polygon polygon to add
     *
     * @throws InvalidValueException when polygon is invalid
     *
     * @return self
     */
    public function addPolygon($polygon)
    {
        $this->polygons[] = $this->validatePolygonValue($polygon);

        return $this;
    }

    /**
     * Polygon getter.
     *
     * @param int $index Index of polygon, use -1 to get last one
     *
     * @return AbstractPolygon
     */
    public function getPolygon($index)
    {
        //TODO replace by a function to be compliant with -1, -2, etc.
        if (-1 == $index) {
            $index = count($this->polygons) - 1;
        }

        $polygonClass = $this->getNamespace().'\Polygon';

        return new $polygonClass($this->polygons[$index], $this->srid);
    }

    /**
     * Polygons getter.
     *
     * @return AbstractPolygon[]
     */
    public function getPolygons()
    {
        $polygons = [];

        for ($i = 0; $i < count($this->polygons); ++$i) {
            $polygons[] = $this->getPolygon($i);
        }

        return $polygons;
    }

    /**
     * Type getter.
     *
     * @return string MultiPolygon
     */
    public function getType()
    {
        return self::MULTIPOLYGON;
    }

    /**
     * Polygon setter.
     *
     * @param AbstractPolygon[] $polygons polygons to set
     *
     * @throws InvalidValueException when a polygon is invalid
     *
     * @return self
     */
    public function setPolygons(array $polygons)
    {
        $this->polygons = $this->validateMultiPolygonValue($polygons);

        return $this;
    }

    /**
     * Convert Polygon into array.
     *
     * @return array[]
     */
    public function toArray()
    {
        return $this->polygons;
    }
}
