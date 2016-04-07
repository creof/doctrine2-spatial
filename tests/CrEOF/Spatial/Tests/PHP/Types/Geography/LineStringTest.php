<?php
/**
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

namespace CrEOF\Spatial\Tests\PHP\Types\Geography;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geography\LineString;
use CrEOF\Spatial\PHP\Types\Geography\Point;

/**
 * LineString object tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group php
 */
class LineStringTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid longitude value "550", must be in range -180 to 180.
     */
    public function testLineStringFromObjectsToArray()
    {
        $expected = array(
            array(550, 550),
            array(551, 551),
            array(552, 552),
            array(553, 553)
        );
        $lineString = new LineString(array(
            new Point(550, 550),
            new Point(551, 551),
            new Point(552, 552),
            new Point(553, 553)
        ));

        $this->assertCount(4, $lineString->getPoints());
        $this->assertEquals($expected, $lineString->toArray());
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid longitude value "550", must be in range -180 to 180.
     */
    public function testLineStringFromArraysGetPoints()
    {
        $expected = array(
            array(550, 550),
            array(551, 551),
            array(552, 552),
            array(553, 553)
        );
        $lineString = new LineString(
            array(
                array(550, 550),
                array(551, 551),
                array(552, 552),
                array(553, 553)
            )
        );
        $actual = $lineString->getPoints();

        $this->assertCount(4, $actual);
        $this->assertEquals($expected, $actual);
    }
}
