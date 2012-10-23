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

namespace CrEOF\Spatial\PHP\Types;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geography\GeographyInterface;

/**
 * Abstract geometry object for spatial types
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractGeometry
{
    const TYPE               = 'geometry';

    const GEOMETRY           = 'geometry';
    const POINT              = 'point';
    const LINESTRING         = 'linestring';
    const POLYGON            = 'polygon';
    const MULTIPOINT         = 'multipoint';
    const MULTILINESTRING    = 'multilinestring';
    const MULTIPOLYGON       = 'multipolygon';
    const GEOMETRYCOLLECTION = 'geometrycollection';

    /**
     * @var int
     */
    protected $srid;

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return array
     */
    abstract public function toArray();

    /**
     * @return string
     */
    public function __toString()
    {
        $type   = strtoupper($this->getType());
        $method = 'toString' . $type;

        return sprintf('%s(%s)', $type, $this->$method($this->toArray()));
    }

    /**
     * @return null|int
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * @param mixed $srid
     *
     * @return self
     */
    public function setSrid($srid)
    {
        $this->srid = (int) $srid;

        return $this;
    }

    /**
     * @param AbstractPoint|array $point
     *
     * @return array
     * @throws InvalidValueException
     */
    protected function validatePointValue($point)
    {
        switch (true) {
            case ($point instanceof AbstractPoint):
                return $point->toArray();
                break;
            case (is_array($point) && count($point) == 2 && is_numeric($point[0]) && is_numeric($point[1])):
                return array_values($point);
                break;
            default:
                throw InvalidValueException::invalidType($this, self::POINT, $point);
        }
    }

    /**
     * @param AbstractPoint[]|array[] $points
     *
     * @return array[]
     */
    protected function validateMultiPointValue(array $points)
    {
        foreach ($points as &$point) {
            $point = $this->validatePointValue($point);
        }

        return $points;
    }

    /**
     * @param AbstractLineString|array[] $ring
     *
     * @return array[]
     * @throws InvalidValueException
     */
    protected function validateRingValue($ring)
    {
        switch (true) {
            case ($ring instanceof AbstractLineString):
                $ring = $ring->toArray();
                break;
            case (is_array($ring)):
                break;
            default:
                throw InvalidValueException::invalidType($this, SELF::LINESTRING, $ring);
        }

        $ring = $this->validateLineStringValue($ring);

        if ($ring[0] !== end($ring)) {
            throw InvalidValueException::ringNotClosed($this->toStringLineString($ring));
        }

        return $ring;
    }

    /**
     * @param AbstractLineString[] $rings
     *
     * @return array
     */
    protected function validatePolygonValue(array $rings)
    {
        foreach ($rings as &$ring) {
            $ring = $this->validateRingValue($ring);
        }

        return $rings;
    }

    /**
     * @param AbstractPoint[]|array[] $points
     *
     * @return array[]
     */
    protected function validateLineStringValue(array $points)
    {
        return $this->validateMultiPointValue($points);
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strrpos($class, '\\') - strlen($class));
    }

    /**
     * @param array $point
     *
     * @return string
     */
    private function toStringPoint(array $point)
    {
        return vsprintf('%s %s', $point);
    }

    /**
     * @param array[] $multiPoint
     *
     * @return string
     */
    private function toStringMultiPoint(array $multiPoint)
    {
        $strings = array();

        foreach ($multiPoint as $point) {
            $strings[] = $this->toStringPoint($point);
        }

        return implode(',', $strings);
    }

    /**
     * @param array[] $lineString
     *
     * @return string
     */
    private function toStringLineString(array $lineString)
    {
        return $this->toStringMultiPoint($lineString);
    }

    /**
     * @param array[] $polygon
     *
     * @return string
     */
    private function toStringPolygon(array $polygon)
    {
        $strings = null;

        foreach ($polygon as $ring) {
            $strings[] = '(' . $this->toStringLineString($ring) . ')';
        }

        return implode(',', $strings);
    }
}
