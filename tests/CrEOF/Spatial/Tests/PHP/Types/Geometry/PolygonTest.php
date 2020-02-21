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

namespace CrEOF\Spatial\Tests\PHP\Types\Geometry;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Polygon object tests.
 *
 * @group php
 *
 * @internal
 * @coversDefaultClass
 */
class PolygonTest extends TestCase
{
    /**
     * Test an empty polygon.
     */
    public function testEmptyPolygon()
    {
        $polygon = new Polygon([]);

        $this->assertEmpty($polygon->getRings());
    }

    /**
     * Test to export json.
     */
    public function testJson()
    {
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $expected = '{"type":"Polygon","coordinates":[[[0,0],[10,0],[10,10],[0,10],[0,0]],[[0,0],[10,0],[10,10],[0,10],[0,0]]]}';
        // phpcs:enable
        $rings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $polygon = new Polygon($rings);

        $this->assertEquals($expected, $polygon->toJson());
    }

    /**
     * Test Polygon with open ring.
     */
    public function testOpenPolygonRing()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid polygon, ring "(0 0,10 0,10 10,0 10)" is not closed');

        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
            ]),
        ];

        new Polygon($rings);
    }

    /**
     * Test to get last ring.
     */
    public function testRingPolygonFromObjectsGetLastRing()
    {
        $ringA = new LineString(
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]
        );
        $ringB = new LineString(
            [
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]
        );
        $polygon = new Polygon([$ringA, $ringB]);

        $this->assertEquals($ringB, $polygon->getRing(-1));
    }

    /**
     * Test to get the first ring.
     */
    public function testRingPolygonFromObjectsGetSingleRing()
    {
        $ringA = new LineString(
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]
        );
        $ringB = new LineString(
            [
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]
        );
        $polygon = new Polygon([$ringA, $ringB]);

        $this->assertEquals($ringA, $polygon->getRing(0));
    }

    /**
     * Test a solid polygon from array add rings.
     *
     * @throws InvalidValueException This should not happen
     */
    public function testSolidPolygonFromArrayAddRings()
    {
        $expected = [
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
            new LineString(
                [
                    new Point(2, 2),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(2, 2),
                ]
            ),
        ];

        $rings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];

        $polygon = new Polygon($rings);

        $polygon->addRing(
            [
                [2, 2],
                [10, 0],
                [10, 10],
                [0, 10],
                [2, 2],
            ]
        );

        $this->assertEquals($expected, $polygon->getRings());
    }

    /**
     * Test a solid polygon from an array of points.
     */
    public function testSolidPolygonFromArrayOfPoints()
    {
        $expected = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $rings = [
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ],
        ];

        $polygon = new Polygon($rings);

        $this->assertEquals($expected, $polygon->toArray());
    }

    /**
     * Test a solid polygon from an array of rings.
     */
    public function testSolidPolygonFromArraysGetRings()
    {
        $expected = [
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
        ];
        $rings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];

        $polygon = new Polygon($rings);

        $this->assertEquals($expected, $polygon->getRings());
    }

    /**
     * Test a solid polygon from arrays to string.
     */
    public function testSolidPolygonFromArraysToString()
    {
        $expected = '(0 0,10 0,10 10,0 10,0 0),(0 0,10 0,10 10,0 10,0 0)';
        $rings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $polygon = new Polygon($rings);
        $result = (string) $polygon;

        $this->assertEquals($expected, $result);
    }

    /**
     * Test solid polygon from objects to array.
     */
    public function testSolidPolygonFromObjectsToArray()
    {
        $expected = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $rings = [
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
        ];

        $polygon = new Polygon($rings);

        $this->assertEquals($expected, $polygon->toArray());
    }
}
