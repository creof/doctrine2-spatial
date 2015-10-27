<?php
/**
 * Copyright (C) 2014 Derek J. Lambert
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

namespace CrEOF\Spatial\PHP\Types\Geography;

use CrEOF\Spatial\PHP\Types\AbstractGeometry;
use CrEOF\Spatial\PHP\Types\AbstractPolygon;

/**
 * MultiPolygon object for MULTIPOLYGON geography type
 *
 * @author  Reno Reckling <reno.reckling@mayflower.de>
 * @license http://dlambert.mit-license.org MIT
 */
class MultiPolygon extends AbstractGeometry implements GeographyInterface
{
    /** @var AbstractPolygon[] */
    protected $polygons;

    /**
     * @param AbstractPolygon[]|array[][] $polygons
     * @param null|int $srid
     */
    public function __construct(array $polygons, $srid = null)
    {
        $this->setPolygons($polygons)
            ->setSrid($srid);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::MULTIPOLYGON;
    }

    /**
     * @param $polygon
     * @return $this
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
     * @param $polygons
     * @return $this
     */
    public function setPolygons($polygons)
    {
        $this->polygons = [];
        foreach ($polygons as $polygon) {
            $this->addPolygon($polygon);
        }

        return $this;
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
     * @return array
     */
    public function toArray()
    {
        return $this->polygons;
    }
}
