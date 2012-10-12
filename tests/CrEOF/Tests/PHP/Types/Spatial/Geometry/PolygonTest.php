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

namespace CrEOF\Tests\PHP\Types\Spatial\Geometry;

use CrEOF\PHP\Types\Spatial\Geometry\LineString;
use CrEOF\PHP\Types\Spatial\Geometry\Point;
use CrEOF\PHP\Types\Spatial\Geometry\Polygon;
/**
 * Polygon object tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class PolygonTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPolygon()
    {
        $rings = new Polygon(array());

        $this->assertEmpty($rings->getRings());
    }

    public function testGoodSolidPolygon()
    {
        $rings = array(
            new LineString(array(
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0)
            ))
        );

        $polygon = new Polygon($rings);

        $this->assertEquals($rings, $polygon->getRings());
    }

    public function testGoodRingPolygon()
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

        $this->assertEquals($rings, $polygon->getRings());
    }

    /**
     * Test polygon with open ring
     *
     * @expectedException        \CrEOF\Exception\InvalidValueException
     * @expectedExceptionMessage Ring "LINESTRING(0 0,10 0,10 10,0 10)" is not closed.
     */
    public function testOpenPolygonRing()
    {
        $rings = array(
            new LineString(array(
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10)
            ))
        );

        $polygon = new Polygon($rings);
    }

    public function testToString()
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
        $result  = (string) $polygon;

        $this->assertEquals('POLYGON((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5))', $result);
    }
}
