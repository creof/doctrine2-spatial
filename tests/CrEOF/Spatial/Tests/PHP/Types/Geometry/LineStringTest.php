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

namespace CrEOF\Spatial\Tests\PHP\Types\Spatial\Geometry;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * LineString object tests.
 *
 * @group php
 *
 * @internal
 * @coversDefaultClass
 */
class LineStringTest extends TestCase
{
    /**
     * Test LineString bad parameter.
     */
    public function testBadLineString()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid LineString Point value of type "integer"');

        new LineString([1, 2, 3, 4]);
    }

    /**
     * Test an empty line string.
     */
    public function testEmptyLineString()
    {
        $lineString = new LineString([]);

        $this->assertEmpty($lineString->getPoints());
    }

    /**
     * Test to convert line string to json.
     */
    public function testJson()
    {
        $expected = '{"type":"LineString","coordinates":[[0,0],[0,5],[5,0],[0,0]]}';

        $lineString = new LineString(
            [
                [0, 0],
                [0, 5],
                [5, 0],
                [0, 0],
            ]
        );
        $this->assertEquals($expected, $lineString->toJson());
    }

    /**
     * Test to get last point.
     */
    public function testLineStringFromArraysGetLastPoint()
    {
        $expected = new Point(3, 3);
        $lineString = new LineString(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $lineString->getPoint(-1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test to get all points of a line string.
     */
    public function testLineStringFromArraysGetPoints()
    {
        $expected = [
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ];
        $lineString = new LineString(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $lineString->getPoints();

        $this->assertCount(4, $actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test to get second point of a linestring.
     */
    public function testLineStringFromArraysGetSinglePoint()
    {
        $expected = new Point(1, 1);
        $lineString = new LineString(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $lineString->getPoint(1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test to verify that a line is closed.
     */
    public function testLineStringFromArraysIsClosed()
    {
        $lineString = new LineString(
            [
                [0, 0],
                [0, 5],
                [5, 0],
                [0, 0],
            ]
        );

        $this->assertTrue($lineString->isClosed());
    }

    /**
     * Test to verify that a line is opened.
     */
    public function testLineStringFromArraysIsOpen()
    {
        $lineString = new LineString(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );

        $this->assertFalse($lineString->isClosed());
    }

    /**
     * Test to convert line to string.
     */
    public function testLineStringFromArraysToString()
    {
        $expected = '0 0,0 5,5 0,0 0';
        $lineString = new LineString(
            [
                [0, 0],
                [0, 5],
                [5, 0],
                [0, 0],
            ]
        );

        $this->assertEquals($expected, (string) $lineString);
    }

    /**
     * Test to convert line to array.
     */
    public function testLineStringFromObjectsToArray()
    {
        $expected = [
            [0, 0],
            [1, 1],
            [2, 2],
            [3, 3],
        ];
        $lineString = new LineString([
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ]);

        $this->assertCount(4, $lineString->getPoints());
        $this->assertEquals($expected, $lineString->toArray());
    }
}
