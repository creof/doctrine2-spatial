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

use CrEOF\Spatial\DBAL\Types\EwktValueParser;
use CrEOF\Spatial\PHP\Types\Geometry\LineString as GeometryLineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon as GeometryPolygon;
use CrEOF\Spatial\PHP\Types\Geography\LineString as GeographyLineString;
use CrEOF\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use CrEOF\Spatial\PHP\Types\Geography\Polygon as GeographyPolygon;

/**
 * EwktValueParser tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group common
 */
class EwktValueParserTest extends \PHPUnit_Framework_TestCase
{
    public function testWktGeometryPoint()
    {
        $point  = new GeometryPoint(5.45, -23.5);
        $string = 'POINT(5.45 -23.5)';
        $parser = new EwktValueParser();
        $result = $parser->parse($string);

        $this->assertEquals($point, $result);
    }

    public function testWktGeometryLineString()
    {
        $lineString  = new GeometryLineString(array(
            new GeometryPoint(5.45, -23.5),
            new GeometryPoint(-4.2, 99),
            new GeometryPoint(23, 57.2345)
        ));
        $string = 'LINESTRING(5.45 -23.5, -4.2 99, 23 57.2345)';
        $parser = new EwktValueParser();
        $result = $parser->parse($string);

        $this->assertEquals($lineString, $result);
    }

    public function testWktGeometryPolygon()
    {
        $rings = array(
            new GeometryLineString(array(
                new GeometryPoint(0, 0),
                new GeometryPoint(10, 0),
                new GeometryPoint(10, 10),
                new GeometryPoint(0, 10),
                new GeometryPoint(0, 0)
            )),
            new GeometryLineString(array(
                new GeometryPoint(5, 5),
                new GeometryPoint(7, 5),
                new GeometryPoint(7, 7),
                new GeometryPoint(5, 7),
                new GeometryPoint(5, 5)
            ))
        );
        $polygon = new GeometryPolygon($rings);
        $string  = 'POLYGON((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5))';
        $parser  = new EwktValueParser();
        $result  = $parser->parse($string);

        $this->assertEquals($polygon, $result);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Unsupported WKT type "triangle".
     */
    public function testWktGeometryUnsupportedType()
    {
        $string = 'TRIANGLE((0 0, 0 9, 9 0, 0 0))';
        $parser = new EwktValueParser;
        $result = $parser->parse($string);
    }

    public function testEwktGeographyPoint()
    {
        $point  = new GeographyPoint(5.45, -23.5, 4326);
        $string = 'SRID=4326;POINT(5.45 -23.5)';
        $parser = new EwktValueParser();
        $result = $parser->parse($string);

        $this->assertEquals($point, $result);
    }

    public function testEwktGeographyLineString()
    {
        $lineString  = new GeographyLineString(
            array(
                new GeographyPoint(5.45, -23.5, 4326),
                new GeographyPoint(-4.2, 99, 4326),
                new GeographyPoint(23, 57.2345, 4326)
            ),
            4326
        );
        $string = 'SRID=4326;LINESTRING(5.45 -23.5, -4.2 99, 23 57.2345)';
        $parser = new EwktValueParser();
        $result = $parser->parse($string);

        $this->assertEquals($lineString, $result);
    }

    public function testEwktGeographyPolygon()
    {
        $rings = array(
            new GeographyLineString(
                array(
                    new GeographyPoint(0, 0, 4326),
                    new GeographyPoint(10, 0, 4326),
                    new GeographyPoint(10, 10, 4326),
                    new GeographyPoint(0, 10, 4326),
                    new GeographyPoint(0, 0, 4326)
                ),
                4326
            ),
            new GeographyLineString(
                array(
                    new GeographyPoint(5, 5, 4326),
                    new GeographyPoint(7, 5, 4326),
                    new GeographyPoint(7, 7, 4326),
                    new GeographyPoint(5, 7, 4326),
                    new GeographyPoint(5, 5, 4326)
                ),
                4326
            )
        );
        $polygon = new GeographyPolygon($rings, 4326);
        $string  = 'SRID=4326;POLYGON((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5))';
        $parser  = new EwktValueParser();
        $result  = $parser->parse($string);

        $this->assertEquals($polygon, $result);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Unsupported EWKT type "triangle".
     */
    public function testEwktGeographyUnsupportedType()
    {
        $string = 'SRID=4326;TRIANGLE((0 0, 0 9, 9 0, 0 0))';
        $parser = new EwktValueParser;
        $result = $parser->parse($string);
    }
}
