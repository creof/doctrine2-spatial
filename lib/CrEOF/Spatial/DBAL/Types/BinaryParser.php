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

/**
 * Parse spatial WKB values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class BinaryParser
{
    const WKB_XDR                = 0;
    const WKB_NDR                = 1;

    const WKB_POINT              = 1;
    const WKB_LINESTRING         = 2;
    const WKB_POLYGON            = 3;
    const WKB_MULTIPOINT         = 4;
    const WKB_MULTILINESTRING    = 5;
    const WKB_MULTIPOLYGON       = 6;
    const WKB_GEOMETRYCOLLECTION = 7;

    const WKB_SRID               = 0x20000000;
    const WKB_M                  = 0x40000000;
    const WKB_Z                  = 0x80000000;

    /**
     * @var int
     */
    private $byteOrder;

    /**
     * @var int
     */
    private $srid;

    /**
     * @var string
     */
    private $input;

    /**
     * @param string $input
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * @return array
     */
    public function parse()
    {
        $this->byteOrder = $this->byteOrder();
        $type            = $this->type();

        if ($this->isExtended($type)) {
            $this->srid = $this->srid();
        }

        $typeName = $this->getTypeName($type);

        return array(
            'type'  => $typeName,
            'srid'  => $this->srid,
            'value' => $this->$typeName()
        );
    }

    /**
     * @param string $format
     *
     * @return array
     */
    private function unPack($format)
    {
        $data        = unpack($format . '/A*value', $this->input);
        $this->input = $data['value'];

        unset($data['value']);

        return $data;
    }

    /**
     * @param int $wkbType
     *
     * @return bool
     */
    private function isExtended($wkbType)
    {
        return ($wkbType & self::WKB_SRID) == self::WKB_SRID;
    }

    /**
     * @param int $wkbType
     *
     * @return string
     * @throws InvalidValueException
     */
    private function getTypeName($wkbType)
    {
        if ($this->isExtended($wkbType)) {
            $wkbType = $wkbType ^ self::WKB_SRID;
        }

        switch ($wkbType) {
            case (self::WKB_POINT):
                $type = AbstractGeometry::POINT;
                break;
            case (self::WKB_LINESTRING):
                $type = AbstractGeometry::LINESTRING;
                break;
            case (self::WKB_POLYGON):
                $type = AbstractGeometry::POLYGON;
                break;
            default:
                throw InvalidValueException::unsupportedWkbType($wkbType);
                break;
        }

        return strtoupper($type);
    }

    /**
     * @return int
     */
    private function byte()
    {
        $data = $this->unPack('Cbyte');

        return $data['byte'];
    }

    /**
     * @return int
     */
    private function long()
    {
        if (self::WKB_XDR == $this->byteOrder) {
            $data = $this->unPack('Nlong');
        } else {
            $data = $this->unPack('Vlong');
        }

        return $data['long'];
    }

    /**
     * @return float
     */
    protected function double()
    {
        $data = $this->unPack('ddouble');

        if (self::WKB_XDR == $this->byteOrder) {
            $data = unpack('ddouble', strrev(pack('d', $data['double'])));
        }

        return $data['double'];
    }

    /**
     * @return int
     */
    private function byteOrder()
    {
        return $this->byte();
    }

    /**
     * @return int
     */
    private function type()
    {
        return $this->long();
    }

    /**
     * @return int
     */
    private function srid()
    {
        return $this->long();
    }


    private function point()
    {
        return array(
            $this->double(),
            $this->double()
        );
    }

    private function lineString()
    {
        $numPoints = $this->long();
        $points    = array();

        for ($j = 0; $j < $numPoints; $j++) {
            $points[] = $this->point();
        }

        return $points;
    }

    private function polygon()
    {
        $numRings = $this->long();
        $rings    = array();

        for ($i = 0; $i < $numRings; $i++) {
            $rings[] = $this->lineString();
        }

        return $rings;
    }
}
