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
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;

/**
 * Abstract geometry object for spatial types.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
abstract class AbstractGeometry implements GeometryInterface
{
    /**
     * Spatial Reference System Identifier.
     *
     * @var int
     */
    protected $srid;

    /**
     * Spatial Reference System Identifier getter.
     *
     * @return int|null
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * Spatial Reference System Identifier fluent setter.
     *
     * @param mixed $srid Spatial Reference System Identifier
     *
     * @return self
     */
    public function setSrid($srid)
    {
        if (null !== $srid) {
            $this->srid = (int) $srid;
        }

        return $this;
    }

    /**
     * Convert this abstract geometry to an array.
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * Convert this abstract geometry to a Json string.
     *
     * @return string
     */
    public function toJson()
    {
        $json = [];
        $json['type'] = $this->getType();
        $json['coordinates'] = $this->toArray();

        return json_encode($json);
    }

    /**
     * Return the namespace of this class.
     *
     * @return string
     */
    protected function getNamespace()
    {
        $class = get_class($this);

        return mb_substr($class, 0, mb_strrpos($class, '\\') - mb_strlen($class));
    }

    /**
     * Validate line strings value.
     *
     * @param AbstractLineString|AbstractPoint[]|array[] $lineString line string to validate
     *
     * @throws InvalidValueException when a point of line string is not valid
     *
     * @return array[]
     */
    protected function validateLineStringValue($lineString)
    {
        return $this->validateMultiPointValue($lineString);
    }

    /**
     * Validate multiline strings value.
     *
     * @param AbstractLineString[] $lineStrings the array of line strings to validate
     *
     * @throws InvalidValueException as soon as a point of a line string is not valid
     *
     * @return array
     */
    protected function validateMultiLineStringValue(array $lineStrings)
    {
        foreach ($lineStrings as &$lineString) {
            $lineString = $this->validateLineStringValue($lineString);
        }

        return $lineStrings;
    }

    /**
     * Validate multi point value.
     *
     * @param AbstractLineString|AbstractPoint[]|array[] $points array of geometric data to validate
     *
     * @throws InvalidValueException when one point is not valid
     *
     * @return array[]
     */
    protected function validateMultiPointValue($points)
    {
        if ($points instanceof GeometryInterface) {
            $points = $points->toArray();
        }

        foreach ($points as &$point) {
            $point = $this->validatePointValue($point);
        }

        return $points;
    }

    /**
     * Validate multi polygon value.
     *
     * @param AbstractPolygon[] $polygons the array of polygons to validate
     *
     * @throws InvalidValueException when one polygon is not valid
     *
     * @return array the validated polygons
     */
    protected function validateMultiPolygonValue(array $polygons)
    {
        foreach ($polygons as &$polygon) {
            if ($polygon instanceof GeometryInterface) {
                $polygon = $polygon->toArray();
            }
            $polygon = $this->validatePolygonValue($polygon);
        }

        return $polygons;
    }

    /**
     * Validate a geometric point or an array of geometric points.
     *
     * @param AbstractPoint|array $point the geometric point(s) to validate
     *
     * @throws InvalidValueException as soon as one point is not valid
     *
     * @return array
     */
    protected function validatePointValue($point)
    {
        switch (true) {
            case $point instanceof AbstractPoint:
                return $point->toArray();
                break;
            case is_array($point) && 2 == count($point) && is_numeric($point[0]) && is_numeric($point[1]):
                return array_values($point);
                break;
            default:
                throw new InvalidValueException(sprintf(
                    'Invalid %s Point value of type "%s"',
                    $this->getType(),
                    (is_object($point) ? get_class($point) : gettype($point))
                ));
        }
    }

    /**
     * Validate polygon values.
     *
     * @param AbstractLineString[] $rings the array of rings
     *
     * @throws InvalidValueException when ring is not valid
     *
     * @return array the validated rings
     */
    protected function validatePolygonValue(array $rings)
    {
        foreach ($rings as &$ring) {
            $ring = $this->validateRingValue($ring);
        }

        return $rings;
    }

    /**
     * Validate ring value.
     *
     * @param AbstractLineString|array[] $ring the ring or a ring converted to array
     *
     * @throws InvalidValueException when the ring is not an abstract line string or is not closed
     *
     * @return array[] the validate ring
     */
    protected function validateRingValue($ring)
    {
        switch (true) {
            case $ring instanceof AbstractLineString:
                $ring = $ring->toArray();
                break;
            case is_array($ring):
                break;
            default:
                throw new InvalidValueException(sprintf(
                    'Invalid %s LineString value of type "%s"',
                    $this->getType(),
                    (is_object($ring) ? get_class($ring) : gettype($ring))
                ));
        }

        $ring = $this->validateLineStringValue($ring);

        if ($ring[0] !== end($ring)) {
            throw new InvalidValueException(sprintf(
                'Invalid polygon, ring "(%s)" is not closed',
                $this->toStringLineString($ring)
            ));
        }

        return $ring;
    }

    /**
     * Convert a line to string.
     *
     * @param array[] $lineString line string already converted into an array
     *
     * @return string
     */
    private function toStringLineString(array $lineString)
    {
        return $this->toStringMultiPoint($lineString);
    }

    /**
     * Convert multiline strings to a string value.
     *
     * @param array[] $multiLineString multi line already converted into an array
     *
     * @return string
     */
    private function toStringMultiLineString(array $multiLineString)
    {
        $strings = null;

        foreach ($multiLineString as $lineString) {
            $strings[] = '('.$this->toStringLineString($lineString).')';
        }

        return implode(',', $strings);
    }

    /**
     * Convert multi points to a string value.
     *
     * @param array[] $multiPoint multipoint already converted into an array of point
     *
     * @return string
     */
    private function toStringMultiPoint(array $multiPoint)
    {
        $strings = [];

        foreach ($multiPoint as $point) {
            $strings[] = $this->toStringPoint($point);
        }

        return implode(',', $strings);
    }

    /**
     * Convert multipolygon to a string.
     *
     * THIS IS NOT A NON USED PRIVATE METHOD.
     *
     * @param array[] $multiPolygon multipolygon already converted into an array of polygon
     *
     * @return string
     */
    private function toStringMultiPolygon(array $multiPolygon)
    {
        $strings = null;

        foreach ($multiPolygon as $polygon) {
            $strings[] = '('.$this->toStringPolygon($polygon).')';
        }

        return implode(',', $strings);
    }

    /**
     * Convert a point to a string value.
     *
     * @param array $point point already converted into an array of TWO coordinates
     *
     * @return string
     */
    private function toStringPoint(array $point)
    {
        return vsprintf('%s %s', $point);
    }

    /**
     * Convert a polygon into a string value.
     *
     * @param array[] $polygon polygons already converted into array
     *
     * @return string
     */
    private function toStringPolygon(array $polygon)
    {
        return $this->toStringMultiLineString($polygon);
    }

    /**
     * Magic method: convert geometry to string.
     *
     * @return string
     */
    public function __toString()
    {
        $type = mb_strtoupper($this->getType());
        $method = 'toString'.$type;

        return $this->{$method}($this->toArray());
    }
}
