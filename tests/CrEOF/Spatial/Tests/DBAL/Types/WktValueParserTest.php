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

use CrEOF\Spatial\DBAL\Types\WktValueParser;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

/**
 * WktValueParser tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group common
 */
class WktValueParserTest extends \PHPUnit_Framework_TestCase
{
    public function testWktPoint()
    {
        $point  = new Point(5.45, -23.5);
        $string = 'POINT(5.45 -23.5)';
        $parser = new WktValueParser();
        $result = $parser->parse($string);

        $this->assertEquals($point, $result);
    }

    public function testWktLineString()
    {
        $lineString  = new LineString(array(
            new Point(5.45, -23.5),
            new Point(-4.2, 99),
            new Point(23, 57.2345)
        ));
        $string = 'LINESTRING(5.45 -23.5, -4.2 99, 23 57.2345)';
        $parser = new WktValueParser();
        $result = $parser->parse($string);

        $this->assertEquals($lineString, $result);
    }

    public function testWktPolygon()
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
        $string  = 'POLYGON((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5))';
        $parser  = new WktValueParser();
        $result  = $parser->parse($string);

        $this->assertEquals($polygon, $result);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Unsupported WKT type "triangle".
     */
    public function testUnsupportedType()
    {
        $string = 'TRIANGLE((0 0, 0 9, 9 0, 0 0))';
        $parser = new WktValueParser;
        $result = $parser->parse($string);
    }
}
