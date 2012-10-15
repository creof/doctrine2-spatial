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

namespace CrEOF\Spatial\Tests\DBAL\Types;

use CrEOF\Spatial\DBAL\Types\WkbValueParser;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

/**
 * WkbValueParser tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group common
 */
class WkbValueParserTest extends \PHPUnit_Framework_TestCase
{
    public function testWkbNdrPoint()
    {
        $point  = new Point(5.45, -23.5);
        $string = '0101000000CDCCCCCCCCCC154000000000008037C0';
        $binary = pack('H*', $string);
        $parser = new WkbValueParser();
        $result = $parser->parse($binary);

        $this->assertEquals($point, $result);
    }

    public function testWkbXdrPoint()
    {
        $point  = new Point(5.45, -23.5);
        $string = '00000000014015CCCCCCCCCCCDC037800000000000';
        $binary = pack('H*', $string);
        $parser = new WkbValueParser();
        $result = $parser->parse($binary);

        $this->assertEquals($point, $result);
    }

    public function testWkbNdrLineString()
    {
        $lineString  = new LineString(array(
            new Point(5.45, -23.5),
            new Point(-4.2, 99),
            new Point(23, 57.2345)
        ));
        $string = '010200000003000000CDCCCCCCCCCC154000000000008037C0CDCCCCCCCCCC10C00000000000C058400000000000003740BC749318049E4C40';
        $binary = pack('H*', $string);
        $parser = new WkbValueParser();
        $result = $parser->parse($binary);

        $this->assertEquals($lineString, $result);
    }

    public function testWkbXdrLineString()
    {
        $lineString  = new LineString(array(
            new Point(5.45, -23.5),
            new Point(-4.2, 99),
            new Point(23, 57.2345)
        ));
        $string = '0000000002000000034015CCCCCCCCCCCDC037800000000000C010CCCCCCCCCCCD4058C000000000004037000000000000404C9E04189374BC';
        $binary = pack('H*', $string);
        $parser = new WkbValueParser();
        $result = $parser->parse($binary);

        $this->assertEquals($lineString, $result);
    }

    public function testWkbNdrPolygon()
    {
        $rings = array(
            new LineString(array(
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0)
            )),
            new LineString(array(
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5)
            ))
        );
        $polygon = new Polygon($rings);
        $string  = '01030000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C4000000000000014400000000000001440';
        $binary  = pack('H*', $string);
        $parser  = new WkbValueParser();
        $result  = $parser->parse($binary);

        $this->assertEquals($polygon, $result);
    }

    public function testWkbXdrPolygon()
    {
        $rings = array(
            new LineString(array(
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0)
            )),
            new LineString(array(
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5)
            ))
        );
        $polygon = new Polygon($rings);
        $string  = '0000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000';
        $binary  = pack('H*', $string);
        $parser  = new WkbValueParser();
        $result  = $parser->parse($binary);

        $this->assertEquals($polygon, $result);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Unsupported WKB type "17".
     */
    public function testNdrUnsupportedType()
    {
        $string = '0111000000CDCCCCCCCCCC154000000000008037C0';
        $binary = pack('H*', $string);
        $parser = new WkbValueParser;
        $result = $parser->parse($binary);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Unsupported WKB type "17".
     */
    public function testXdrUnsupportedType()
    {
        $string = '00000000114015CCCCCCCCCCCDC037800000000000';
        $binary = pack('H*', $string);
        $parser = new WkbValueParser;
        $result = $parser->parse($binary);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid byte order "2".
     */
    public function testInvalidByteOrder()
    {
        $string = '0201000000CDCCCCCCCCCC154000000000008037C0';
        $binary = pack('H*', $string);
        $parser = new WkbValueParser;
        $result = $parser->parse($binary);
    }
}
