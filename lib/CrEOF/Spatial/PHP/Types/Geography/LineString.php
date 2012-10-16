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

namespace CrEOF\Spatial\PHP\Types\Geography;

use CrEOF\Spatial\PHP\Types\AbstractLineString;
use CrEOF\Spatial\PHP\Types\AbstractPoint;

/**
 * LineString object for LINESTRING geography type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class LineString extends AbstractLineString implements GeographyInterface
{
    /**
     * @var int
     */
    protected $srid;

    /**
     * @param AbstractPoint[] $points
     * @param int|null $srid
     */
    public function __construct(array $points, $srid = null)
    {
        $this->setPoints($points);
    }

    /**
     * @return int
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * @param int $srid
     *
     * @return self
     */
    public function setSrid($srid)
    {
        $this->srid = ($srid ? $srid : self::DEFAULT_SRID);

        return $this;
    }
}
