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
 * Parse spatial EWKB values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class EwkbValueParser extends WkbValueParser
{
    const WKB_SRID = 0x20000000;
    const WKB_M    = 0x40000000;
    const WKB_Z    = 0x80000000;

    /**
     * @var int
     */
    private $srid;

    /**
     * @param string $value
     *
     * @return AbstractGeometry
     * @throws InvalidValueException
     */
    public function parse($value)
    {
        $data            = unpack('CbyteOrder/A*value', $value);
        $this->byteOrder = $data['byteOrder'];

        switch ($this->byteOrder) {
            case 0:
                $data = unpack('NewkbType/A*value', $data['value']);
                break;
            case 1:
                $data = unpack('VewkbType/A*value', $data['value']);
                break;
            default:
                throw InvalidValueException::invalidByteOrder($this->byteOrder);
                break;
        }

        $ewkbType   = $data['ewkbType'];

        if ( ! $this->isExtended($ewkbType)) {
            $method = 'convertTo' . $this->getTypeName($ewkbType);

            return parent::$method($data['value']);
        }

        $data       = $this->unpackEwkbSrid($data['value']);
        $this->srid = $data['srid'];
        $method     = 'convertTo' . $this->getExtendedTypeName($ewkbType);

        return $this->$method($data['value']);
    }

    private function isExtended($type)
    {
        return ($type & self::WKB_SRID) == self::WKB_SRID;
    }

    private function getExtendedTypeName($type)
    {
        return $this->getTypeName($type ^ self::WKB_SRID);
    }

    /**
     * @param string $value
     *
     * @return array
     * @throws InvalidValueException
     */
    private function unpackEwkbSrid($value)
    {
        switch ($this->byteOrder) {
            case 0:
                return unpack('Nsrid/A*value', $value);
                break;
            case 1:
                return unpack('Vsrid/A*value', $value);
                break;
            default:
                throw InvalidValueException::invalidByteOrder($this->byteOrder);
                break;
        }
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Point
     */
    protected function createPoint($latitude, $longitude)
    {
        if ( ! $this->srid) {
            return parent::createPoint($latitude, $longitude);
        }

        return new Point($latitude, $longitude, $this->srid);
    }

    /**
     * @param Point[] $points
     *
     * @return LineString
     */
    protected function createLineString(array $points)
    {
        if ( ! $this->srid) {
            return parent::createLineString($points);
        }

        return new LineString($points, $this->srid);
    }

    /**
     * @param LineString[] $rings
     *
     * @return Polygon
     */
    protected function createPolygon(array $rings)
    {
        if ( ! $this->srid) {
            return parent::createPolygon($rings);
        }

        return new Polygon($rings, $this->srid);
    }
}
