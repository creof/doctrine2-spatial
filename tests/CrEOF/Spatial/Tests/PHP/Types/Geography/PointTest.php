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

namespace CrEOF\Spatial\Tests\PHP\Types\Geography;

use CrEOF\Spatial\PHP\Types\Geography\Point;

/**
 * Point object tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group php
 */
class PointTest extends \PHPUnit_Framework_TestCase
{
    public function testGoodNumericPoint()
    {
        $point1 = new Point(-73.7562317, 42.6525793);

        $this->assertEquals(42.6525793, $point1->getLatitude());
        $this->assertEquals(-73.7562317, $point1->getLongitude());
    }

    public function testGoodStringPoints()
    {
        $point2 = new Point('79:56:55W', '40:26:46N');

        $this->assertEquals(40.446111111111, $point2->getLatitude());
        $this->assertEquals(-79.948611111111, $point2->getLongitude());

        $point3 = new Point('79°56\'55"W', '40°26\'46"N');

        $this->assertEquals(40.446111111111, $point3->getLatitude());
        $this->assertEquals(-79.948611111111, $point3->getLongitude());

        $point4 = new Point('79° 56\' 55" W', '40° 26\' 46" N');

        $this->assertEquals(40.446111111111, $point4->getLatitude());
        $this->assertEquals(-79.948611111111, $point4->getLongitude());

        $point5 = new Point('79°56′55″W', '40°26′46″N');

        $this->assertEquals(40.446111111111, $point5->getLatitude());
        $this->assertEquals(-79.948611111111, $point5->getLongitude());

        $point6 = new Point('79° 56′ 55″ W', '40° 26′ 46″ N');

        $this->assertEquals(40.446111111111, $point6->getLatitude());
        $this->assertEquals(-79.948611111111, $point6->getLongitude());

        $point7 = new Point('79:56:55.832W', '40:26:46.543N');

        $this->assertEquals(40.446261944444, $point7->getLatitude());
        $this->assertEquals(-79.948842222222, $point7->getLongitude());

        $point7 = new Point('112:4:0W', '33:27:0N');

        $this->assertEquals(33.45, $point7->getLatitude());
        $this->assertEquals(-112.06666666667, $point7->getLongitude());
    }

    /**
     * Test bad string parameters - invalid latitude direction
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 84:26:46Q is not a valid coordinate value.
     */
    public function testBadLatitudeDirection()
    {
        new Point('100:56:55W', '84:26:46Q');
    }

    /**
     * Test bad string parameters - latitude degrees greater that 90
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 92:26:46N is not a valid coordinate value.
     */
    public function testBadLatitudeDegrees()
    {
        new Point('79:56:55W', '92:26:46N');
    }

    /**
     * Test bad string parameters - latitude minutes greater than 59
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 84:64:46N is not a valid coordinate value.
     */
    public function testBadLatitudeMinutes()
    {
        new Point('108:42:55W', '84:64:46N');
    }

    /**
     * Test bad string parameters - latitude seconds greater than 59
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 84:23:75N is not a valid coordinate value.
     */
    public function testBadLatitudeSeconds()
    {
        new Point('108:42:55W', '84:23:75N');
    }

    /**
     * Test bad string parameters - invalid longitude direction
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 100:56:55P is not a valid coordinate value.
     */
    public function testBadLongitudeDirection()
    {
        new Point('100:56:55P', '84:26:46N');
    }

    /**
     * Test bad string parameters - longitude degrees greater than 180
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 190:56:55W is not a valid coordinate value.
     */
    public function testBadLongitudeDegrees()
    {
        new Point('190:56:55W', '84:26:46N');
    }

    /**
     * Test bad string parameters - longitude minutes greater than 59
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 108:62:55W is not a valid coordinate value.
     */
    public function testBadLongitudeMinutes()
    {
        new Point('108:62:55W', '84:26:46N');
    }

    /**
     * Test bad string parameters - longitude seconds greater than 59
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage 108:53:94W is not a valid coordinate value.
     */
    public function testBadLongitudeSeconds()
    {
        new Point('108:53:94W', '84:26:46N');
    }

    public function testToArray()
    {
        $expected = array(10, 10);
        $point    = new Point(10, 10);
        $result   = $point->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testPointWithSrid()
    {
        $point  = new Point(10, 10, 4326);
        $result = $point->getSrid();

        $this->assertEquals(4326, $result);
    }

    public function testGetType()
    {
        $point  = new Point(10, 10);
        $result = $point->getType();

        $this->assertEquals('Point', $result);
    }

    public function testPointFromArrayToString()
    {
        $expected = '5 5';
        $point    = new Point(array(5, 5));

        $this->assertEquals($expected, (string) $point);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid parameters passed to CrEOF\Spatial\PHP\Types\Geography\Point::__construct: "5", "5", "5", "5"
     */
    public function testPointTooManyArguments()
    {
        new Point(5, 5, 5, 5);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid parameters passed to CrEOF\Spatial\PHP\Types\Geography\Point::__construct: Array, Array, "1234"
     */
    public function testPointWrongArgumentTypes()
    {
        new Point(array(), array(), '1234');
    }

    /**
     * Test bad numeric parameters - latitude greater than 90
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid latitude value "190", must be in range -90 to 90.
     */
    public function testBadNumericGreaterThanLatitude()
    {
        $point = new Point(55, 190);
    }

    /**
     * Test bad numeric parameters - latitude less than -90
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid latitude value "-90.00001", must be in range -90 to 90.
     */
    public function testBadNumericLessThanLatitude()
    {
        $point = new Point(55, -90.00001);
    }

    /**
     * Test bad numeric parameters - longitude greater than 180
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid longitude value "180.134", must be in range -180 to 180.
     */
    public function testBadNumericGreaterThanLongitude()
    {
        $point = new Point(180.134, 54);
    }

    /**
     * Test bad numeric parameters - longitude less than -180
     *
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid longitude value "-230", must be in range -180 to 180.
     */
    public function testBadNumericLessThanLongitude()
    {
        $point = new Point(-230, 54);
    }
}
