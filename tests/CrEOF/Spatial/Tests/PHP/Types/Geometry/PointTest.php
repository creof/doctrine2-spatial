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
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * Point object tests.
 *
 * @group php
 *
 * @internal
 * @coversDefaultClass
 */
class PointTest extends TestCase
{
    /**
     * Test bad string parameters - latitude degrees greater that 90.
     */
    public function testBadLatitudeDegrees()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Degrees out of range -90 to 90 in value "92:26:46N"');

        new Point('79:56:55W', '92:26:46N');
    }

    /**
     * Test bad string parameters - invalid latitude direction.
     */
    public function testBadLatitudeDirection()
    {
        $this->expectException(InvalidValueException::class);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this->expectExceptionMessage('[Syntax Error] line 0, col 8: Error: Expected CrEOF\\Geo\\String\\Lexer::T_INTEGER or CrEOF\\Geo\\String\\Lexer::T_FLOAT, got "Q" in value "84:26:46Q"');
        // phpcs:enable

        new Point('100:56:55W', '84:26:46Q');
    }

    /**
     * Test bad string parameters - latitude minutes greater than 59.
     */
    public function testBadLatitudeMinutes()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Minutes greater than 60 in value "84:64:46N"');

        new Point('108:42:55W', '84:64:46N');
    }

    /**
     * Test bad string parameters - latitude seconds greater than 59.
     */
    public function testBadLatitudeSeconds()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Seconds greater than 60 in value "84:23:75N"');

        new Point('108:42:55W', '84:23:75N');
    }

    /**
     * Test bad string parameters - longitude degrees greater than 180.
     */
    public function testBadLongitudeDegrees()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Degrees out of range -180 to 180 in value "190:56:55W"');

        new Point('190:56:55W', '84:26:46N');
    }

    /**
     * Test bad string parameters - invalid longitude direction.
     */
    public function testBadLongitudeDirection()
    {
        $this->expectException(InvalidValueException::class);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this->expectExceptionMessage('[Syntax Error] line 0, col 9: Error: Expected CrEOF\\Geo\\String\\Lexer::T_INTEGER or CrEOF\\Geo\\String\\Lexer::T_FLOAT, got "P" in value "100:56:55P"');
        // phpcs:enable

        new Point('100:56:55P', '84:26:46N');
    }

    /**
     * Test bad string parameters - longitude minutes greater than 59.
     */
    public function testBadLongitudeMinutes()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Minutes greater than 60 in value "108:62:55W"');

        new Point('108:62:55W', '84:26:46N');
    }

    /**
     * Test bad string parameters - longitude seconds greater than 59.
     */
    public function testBadLongitudeSeconds()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Seconds greater than 60 in value "108:53:94W"');

        new Point('108:53:94W', '84:26:46N');
    }

    /**
     * Test getType method.
     */
    public function testGetType()
    {
        $point = new Point(10, 10);
        $result = $point->getType();

        static::assertEquals('Point', $result);
    }

    /**
     * Test a valid numeric point.
     */
    public function testGoodNumericPoint()
    {
        $point = new Point(-73.7562317, 42.6525793);

        static::assertEquals(42.6525793, $point->getLatitude());
        static::assertEquals(-73.7562317, $point->getLongitude());

        $point
            ->setLatitude(40.446111111111)
            ->setLongitude(-79.948611111111)
        ;

        static::assertEquals(40.446111111111, $point->getLatitude());
        static::assertEquals(-79.948611111111, $point->getLongitude());
    }

    /**
     * Test valid string points.
     */
    public function testGoodStringPoints()
    {
        $point = new Point('79:56:55W', '40:26:46N');

        static::assertEquals(40.446111111111, $point->getLatitude());
        static::assertEquals(-79.948611111111, $point->getLongitude());

        $point = new Point('79°56\'55"W', '40°26\'46"N');

        static::assertEquals(40.446111111111, $point->getLatitude());
        static::assertEquals(-79.948611111111, $point->getLongitude());

        $point = new Point('79° 56\' 55" W', '40° 26\' 46" N');

        static::assertEquals(40.446111111111, $point->getLatitude());
        static::assertEquals(-79.948611111111, $point->getLongitude());

        $point = new Point('79°56′55″W', '40°26′46″N');

        static::assertEquals(40.446111111111, $point->getLatitude());
        static::assertEquals(-79.948611111111, $point->getLongitude());

        $point = new Point('79° 56′ 55″ W', '40° 26′ 46″ N');

        static::assertEquals(40.446111111111, $point->getLatitude());
        static::assertEquals(-79.948611111111, $point->getLongitude());

        $point = new Point('79:56:55.832W', '40:26:46.543N');

        static::assertEquals(40.446261944444, $point->getLatitude());
        static::assertEquals(-79.948842222222, $point->getLongitude());

        $point = new Point('112:4:0W', '33:27:0N');

        static::assertEquals(33.45, $point->getLatitude());
        static::assertEquals(-112.06666666667, $point->getLongitude());
    }

    /**
     * Test to convert point to json.
     */
    public function testJson()
    {
        $expected = '{"type":"Point","coordinates":[5,5]}';
        $point = new Point([5, 5]);

        static::assertEquals($expected, $point->toJson());
    }

    /**
     * Test bad string parameters - No parameters.
     */
    public function testMissingArguments()
    {
        $this->expectException(InvalidValueException::class);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this->expectExceptionMessage('Invalid parameters passed to CrEOF\\Spatial\\PHP\\Types\\Geometry\\Point::__construct:');
        // phpcs:enable

        new Point();
    }

    /**
     * Test a point created with an array.
     */
    public function testPointFromArrayToString()
    {
        $expected = '5 5';
        $point = new Point([5, 5]);

        static::assertEquals($expected, (string) $point);
    }

    /**
     * Test error when point is created with too many arguments.
     */
    public function testPointTooManyArguments()
    {
        $this->expectException(InvalidValueException::class);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this->expectExceptionMessage('Invalid parameters passed to CrEOF\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: "5", "5", "5", "5"');
        // phpcs:enable

        new Point(5, 5, 5, 5);
    }

    /**
     * Test point with srid.
     */
    public function testPointWithSrid()
    {
        $point = new Point(10, 10, 2154);
        $result = $point->getSrid();

        static::assertEquals(2154, $result);
    }

    /**
     * Test error when point was created with wrong arguments type.
     */
    public function testPointWrongArgumentTypes()
    {
        $this->expectException(InvalidValueException::class);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this->expectExceptionMessage('Invalid parameters passed to CrEOF\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: Array, Array, "1234"');
        // phpcs:enable

        new Point([], [], '1234');
    }

    /**
     * Test to convert a point to an array.
     */
    public function testToArray()
    {
        $expected = [10, 10];
        $point = new Point(10, 10);
        $result = $point->toArray();

        static::assertEquals($expected, $result);
    }

    /**
     * Test bad string parameters - Two invalid parameters.
     */
    public function testTwoInvalidArguments()
    {
        $this->expectException(InvalidValueException::class);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this->expectExceptionMessage('Invalid parameters passed to CrEOF\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: "", ""');
        // phpcs:enable

        new Point(null, null);
    }

    /**
     * Test bad string parameters - More than 3 parameters.
     */
    public function testUnusedArguments()
    {
        $this->expectException(InvalidValueException::class);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $this->expectExceptionMessage('Invalid parameters passed to CrEOF\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: "1", "2", "3", "4", "", "5"');
        // phpcs:enable

        new Point(1, 2, 3, 4, null, 5);
    }
}
