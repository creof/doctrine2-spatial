<?php
/**
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

/**
 * Abstract Polygon object for POLYGON spatial types
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractMultiPolygon extends AbstractGeometry
{
    /**
     * @var array[] $polygons
     */
    protected $polygons = array();

    /**
     * @param AbstractPolygon[]|array[] $polygons
     * @param null|int                     $srid
     */
    public function __construct(array $polygons, $srid = null)
    {
        $this->setPolygons($polygons)
            ->setSrid($srid);
    }

    /**
     * @param AbstractPolygon|array[] $polygon
     *
     * @return self
     */
    public function addPolygon($polygon)
    {
        $this->polygons[] = $this->validatePolygonValue($polygon);

        return $this;
    }

    /**
     * @return AbstractPolygon[]
     */
    public function getPolygons()
    {
        $polygons = array();

        for ($i = 0; $i < count($this->polygons); $i++) {
            $polygons[] = $this->getPolygon($i);
        }

        return $polygons;
    }

    /**
     * @param int $index
     *
     * @return AbstractPolygon
     */
    public function getPolygon($index)
    {
        if (-1 == $index) {
            $index = count($this->polygons) - 1;
        }

        $polygonClass = $this->getNamespace() . '\Polygon';

        return new $polygonClass($this->polygons[$index], $this->srid);
    }

    /**
     * @param AbstractPolygon[] $polygons
     *
     * @return self
     */
    public function setPolygons(array $polygons)
    {
        $this->polygons = $this->validateMultiPolygonValue($polygons);

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::MULTIPOLYGON;
    }

    /**
     * @return array[]
     */
    public function toArray()
    {
        return $this->polygons;
    }
}
