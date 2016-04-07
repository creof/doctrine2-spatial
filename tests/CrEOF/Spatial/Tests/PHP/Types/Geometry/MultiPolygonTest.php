<?php


namespace CrEOF\Spatial\Tests\PHP\Types\Geometry;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\MultiPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
/**
 * Polygon object tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group php
 */
class MultiPolygonTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyMultiPolygon()
    {
        $multiPolygon = new MultiPolygon(array());

        $this->assertEmpty($multiPolygon->getPolygons());
    }

    public function testSolidMultiPolygonFromObjectsToArray()
    {
        $expected = array(
            array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            ),
            array(
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                    array(5, 5)
                )
            )
        );

        $polygons = array(
            new Polygon(
                array(
                    new LineString(
                        array(
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0)
                        )
                    )
                )
            ),
            new Polygon(
                array(
                    new LineString(
                        array(
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5)
                        )
                    )
                )
            )
        );

        $multiPolygon = new MultiPolygon($polygons);

        $this->assertEquals($expected, $multiPolygon->toArray());
    }

    public function testSolidMultiPolygonFromArraysGetPolygons()
    {
        $expected = array(
            new Polygon(
                array(
                    new LineString(
                        array(
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0)
                        )
                    )
                )
            ),
            new Polygon(
                array(
                    new LineString(
                        array(
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5)
                        )
                    )
                )
            )
        );

        $polygons = array(
            array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            ),
            array(
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                    array(5, 5)
                )
            )
        );


        $multiPolygon = new MultiPolygon($polygons);

        $this->assertEquals($expected, $multiPolygon->getPolygons());
    }


    public function testSolidMultiPolygonAddPolygon()
    {
        $expected = array(
            new Polygon(
                array(
                    new LineString(
                        array(
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0)
                        )
                    )
                )
            ),
            new Polygon(
                array(
                    new LineString(
                        array(
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5)
                        )
                    )
                )
            )
        );


        $polygon =  new Polygon(
            array (
                new LineString(
                    array (
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    )
                ),
            )
        );


        $multiPolygon = new MultiPolygon(array($polygon));

        $multiPolygon->addPolygon(
            array (
                array (
                    new Point(5, 5),
                    new Point(7, 5),
                    new Point(7, 7),
                    new Point(5, 7),
                    new Point(5, 5),
                ),
            )
        );

        $this->assertEquals($expected, $multiPolygon->getPolygons());
    }



    public function testMultiPolygonFromObjectsGetSinglePolygon()
    {
        $polygon1 = new Polygon(
            array(
                new LineString(
                    array(
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0)
                    )
                )
            )
        );
        $polygon2 = new Polygon(
            array(
                new LineString(
                    array(
                        new Point(5, 5),
                        new Point(7, 5),
                        new Point(7, 7),
                        new Point(5, 7),
                        new Point(5, 5)
                    )
                )
            )
        );
        $multiPolygon = new MultiPolygon(array($polygon1, $polygon2));

        $this->assertEquals($polygon1, $multiPolygon->getPolygon(0));
    }

    public function testMultiPolygonFromObjectsGetLastPolygon()
    {
        $polygon1 = new Polygon(
            array(
                new LineString(
                    array(
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0)
                    )
                )
            )
        );
        $polygon2 = new Polygon(
            array(
                new LineString(
                    array(
                        new Point(5, 5),
                        new Point(7, 5),
                        new Point(7, 7),
                        new Point(5, 7),
                        new Point(5, 5)
                    )
                )
            )
        );
        $multiPolygon = new MultiPolygon(array($polygon1, $polygon2));

        $this->assertEquals($polygon2, $multiPolygon->getPolygon(-1));
    }

    public function testSolidMultiPolygonFromArraysToString()
    {
        $expected = '((0 0,10 0,10 10,0 10,0 0)),((5 5,7 5,7 7,5 7,5 5))';
        $polygons = array(
            array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            ),
            array(
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                    array(5, 5)
                )
            )
        );
        $multiPolygon = new MultiPolygon($polygons);
        $result  = (string) $multiPolygon;

        $this->assertEquals($expected, $result);
    }

    public function testJson()
    {
        $expected = '{"type":"MultiPolygon","coordinates":[[[[0,0],[10,0],[10,10],[0,10],[0,0]]],[[[5,5],[7,5],[7,7],[5,7],[5,5]]]]}';
        $polygons = array(
            array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            ),
            array(
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                    array(5, 5)
                )
            )
        );
        $multiPolygon = new MultiPolygon($polygons);

        $this->assertEquals($expected, $multiPolygon->toJson());
    }
}
