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

use CrEOF\Spatial\DBAL\Types\StringParser;

/**
 * StringParser tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group result_processing
 */
class StringParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 0: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_TYPE, got "PNT" in value "PNT(10 10)"
     */
    public function testParsingBadType()
    {
        $value  = 'PNT(10 10)';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingPointValue()
    {
        $value    = 'POINT(34.23 -87)';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingPointValueWithSrid()
    {
        $value    = 'SRID=4326;POINT(34.23 -87)';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 5: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_INTEGER, got "432.6" in value "SRID=432.6;POINT(34.23 -87)"
     */
    public function testParsingPointValueWithBadSrid()
    {
        $value  = 'SRID=432.6;POINT(34.23 -87)';
        $parser = new StringParser($value);

        $parser->parse();
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 11: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_INTEGER, got ")" in value "POINT(34.23)"
     */
    public function testParsingPointValueMissingCoordinate()
    {
        $value  = 'POINT(34.23)';
        $parser = new StringParser($value);

        $parser->parse();
    }


    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 8: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_INTEGER, got "," in value "POINT(10, 10)"
     */
    public function testParsingPointValueWithComma()
    {
        $value  = 'POINT(10, 10)';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingLineStringValue()
    {
        $value    = 'LINESTRING(34.23 -87, 45.3 -92)';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'LINESTRING',
            'value' => array(
                array(34.23, -87),
                array(45.3, -92)
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingLineStringValueWithSrid()
    {
        $value    = 'SRID=4326;LINESTRING(34.23 -87, 45.3 -92)';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'LINESTRING',
            'value' => array(
                array(34.23, -87),
                array(45.3, -92)
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 21: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_CLOSE_PARENTHESIS, got "45.3" in value "LINESTRING(34.23 -87 45.3 -92)"
     */
    public function testParsingLineStringValueMissingComma()
    {
        $value  = 'LINESTRING(34.23 -87 45.3 -92)';
        $parser = new StringParser($value);

        $parser->parse();
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 26: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_INTEGER, got ")" in value "LINESTRING(34.23 -87, 45.3)"
     */
    public function testParsingLineStringValueMissingCoordinate()
    {
        $value  = 'LINESTRING(34.23 -87, 45.3)';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingPolygonValue()
    {
        $value    = 'POLYGON((0 0,10 0,10 10,0 10,0 0))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'POLYGON',
            'value' => array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingPolygonValueWithSrid()
    {
        $value    = 'SRID=4326;POLYGON((0 0,10 0,10 10,0 10,0 0))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'POLYGON',
            'value' => array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 8: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_OPEN_PARENTHESIS, got "0" in value "POLYGON(0 0,10 0,10 10,0 10,0 0)"
     */
    public function testParsingPolygonValueMissingParenthesis()
    {
        $value  = 'POLYGON(0 0,10 0,10 10,0 10,0 0)';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingMultiRingPolygonValue()
    {
        $value    = 'POLYGON((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'POLYGON',
            'value' => array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                ),
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                    array(5, 5)
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingMultiRingPolygonValueWithSrid()
    {
        $value    = 'SRID=4326;POLYGON((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'POLYGON',
            'value' => array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                    array(0, 0)
                ),
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                    array(5, 5)
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 33: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_CLOSE_PARENTHESIS, got "(" in value "POLYGON((0 0,10 0,10 10,0 10,0 0)(5 5,7 5,7 7,5 7,5 5))"
     */
    public function testParsingMultiRingPolygonValueMissingComma()
    {
        $value  = 'POLYGON((0 0,10 0,10 10,0 10,0 0)(5 5,7 5,7 7,5 7,5 5))';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingMultiPointValue()
    {
        $value    = 'MULTIPOINT(0 0,10 0,10 10,0 10)';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'MULTIPOINT',
            'value' => array(
                array(0, 0),
                array(10, 0),
                array(10, 10),
                array(0, 10)
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingMultiPointValueWithSrid()
    {
        $value    = 'SRID=4326;MULTIPOINT(0 0,10 0,10 10,0 10)';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'MULTIPOINT',
            'value' => array(
                array(0, 0),
                array(10, 0),
                array(10, 10),
                array(0, 10)
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 11: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_INTEGER, got "(" in value "MULTIPOINT((0 0,10 0,10 10,0 10))"
     */
    public function testParsingMultiPointValueWithExtraParenthesis()
    {
        $value  = 'MULTIPOINT((0 0,10 0,10 10,0 10))';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingMultiLineStringValue()
    {
        $value    = 'MULTILINESTRING((0 0,10 0,10 10,0 10),(5 5,7 5,7 7,5 7))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'MULTILINESTRING',
            'value' => array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                ),
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingMultiLineStringValueWithSrid()
    {
        $value    = 'SRID=4326;MULTILINESTRING((0 0,10 0,10 10,0 10),(5 5,7 5,7 7,5 7))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'MULTILINESTRING',
            'value' => array(
                array(
                    array(0, 0),
                    array(10, 0),
                    array(10, 10),
                    array(0, 10),
                ),
                array(
                    array(5, 5),
                    array(7, 5),
                    array(7, 7),
                    array(5, 7),
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 37: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_CLOSE_PARENTHESIS, got "(" in value "MULTILINESTRING((0 0,10 0,10 10,0 10)(5 5,7 5,7 7,5 7))"
     */
    public function testParsingMultiLineStringValueMissingComma()
    {
        $value  = 'MULTILINESTRING((0 0,10 0,10 10,0 10)(5 5,7 5,7 7,5 7))';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingMultiPolygonValue()
    {
        $value    = 'MULTIPOLYGON(((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5)),((1 1, 3 1, 3 3, 1 3, 1 1)))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'MULTIPOLYGON',
            'value' => array(
                array(
                    array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10),
                        array(0, 0)
                    ),
                    array(
                        array(5, 5),
                        array(7, 5),
                        array(7, 7),
                        array(5, 7),
                        array(5, 5)
                    )
                ),
                array(
                    array(
                        array(1, 1),
                        array(3, 1),
                        array(3, 3),
                        array(1, 3),
                        array(1, 1)
                    )
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingMultiPolygonValueWithSrid()
    {
        $value    = 'SRID=4326;MULTIPOLYGON(((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5)),((1 1, 3 1, 3 3, 1 3, 1 1)))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'MULTIPOLYGON',
            'value' => array(
                array(
                    array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10),
                        array(0, 0)
                    ),
                    array(
                        array(5, 5),
                        array(7, 5),
                        array(7, 7),
                        array(5, 7),
                        array(5, 5)
                    )
                ),
                array(
                    array(
                        array(1, 1),
                        array(3, 1),
                        array(3, 3),
                        array(1, 3),
                        array(1, 1)
                    )
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 64: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_OPEN_PARENTHESIS, got "1" in value "MULTIPOLYGON(((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5)),(1 1, 3 1, 3 3, 1 3, 1 1))"
     */
    public function testParsingMultiPolygonValueMissingParenthesis()
    {
        $value  = 'MULTIPOLYGON(((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5)),(1 1, 3 1, 3 3, 1 3, 1 1))';
        $parser = new StringParser($value);

        $parser->parse();
    }

    public function testParsingGeometryCollectionValue()
    {
        $value    = 'GEOMETRYCOLLECTION(POINT(10 10), POINT(30 30), LINESTRING(15 15, 20 20))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'GEOMETRYCOLLECTION',
            'value' => array(
                array(
                    'type'  => 'POINT',
                    'value' => array(10, 10)
                ),
                array(
                    'type'  => 'POINT',
                    'value' => array(30, 30)
                ),
                array(
                    'type'  => 'LINESTRING',
                    'value' => array(
                        array(15, 15),
                        array(20, 20)
                    )
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingGeometryCollectionValueWithSrid()
    {
        $value    = 'SRID=4326;GEOMETRYCOLLECTION(POINT(10 10), POINT(30 30), LINESTRING(15 15, 20 20))';
        $parser   = new StringParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'GEOMETRYCOLLECTION',
            'value' => array(
                array(
                    'type'  => 'POINT',
                    'value' => array(10, 10)
                ),
                array(
                    'type'  => 'POINT',
                    'value' => array(30, 30)
                ),
                array(
                    'type'  => 'LINESTRING',
                    'value' => array(
                        array(15, 15),
                        array(20, 20)
                    )
                )
            )
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage [Syntax Error] line 0, col 19: Error: Expected CrEOF\Spatial\DBAL\Types\StringLexer::T_TYPE, got "PNT" in value "GEOMETRYCOLLECTION(PNT(10 10), POINT(30 30), LINESTRING(15 15, 20 20))"
     */
    public function testParsingGeometryCollectionValueWithBadType()
    {
        $value  = 'GEOMETRYCOLLECTION(PNT(10 10), POINT(30 30), LINESTRING(15 15, 20 20))';
        $parser = new StringParser($value);

        $parser->parse();
    }
}
