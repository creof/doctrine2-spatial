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

namespace CrEOF\Spatial\DBAL\Types;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\AbstractGeometry;
use CrEOF\Spatial\PHP\Types\Geography\LineString;
use CrEOF\Spatial\PHP\Types\Geography\Point;
use CrEOF\Spatial\PHP\Types\Geography\Polygon;

/**
 * Parse spatial EWKT values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class EwktValueParser extends WktValueParser
{
    private $srid;

    /**
     * @param string $string
     *
     * @return AbstractGeometry
     */
    public function parse($string)
    {
        if ( ! $string) {
            return null;
        }

        $this->srid = null;
        $pattern    = '/^(?:SRID=(?<srid>\d+);)?(?<value>.*)$/';

        preg_match($pattern, $string, $matches);

        if (isset($matches['srid'])) {
            $this->srid = $matches['srid'];
        }

        $object = parent::parse($matches['value']);

        return $object->setSrid($this->srid);
    }

    protected function finalize()
    {
        if ( ! $this->srid) {
            return parent::finalize();
        }

        switch ($this->type) {
            case AbstractGeometry::POINT:
                return $this->current[0];
                break;
            case AbstractGeometry::LINESTRING:
                return new LineString($this->current, $this->srid);
                break;
            case AbstractGeometry::POLYGON:
                return new Polygon($this->current, $this->srid);
                break;
            default:
                throw InvalidValueException::unsupportedEwktType($this->type);
                break;
        }
    }

    protected function createPoint()
    {
        if ( ! $this->srid) {
            return parent::createPoint();
        }

        return new Point($this->point[0], $this->point[1], $this->srid);
    }

    protected function createLineString()
    {
        if ( ! $this->srid) {
            return parent::createLineString();
        }

        return new LineString($this->current, $this->srid);
    }

}
