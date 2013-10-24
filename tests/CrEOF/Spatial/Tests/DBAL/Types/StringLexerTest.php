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

use CrEOF\Spatial\DBAL\Types\StringLexer;

/**
 * StringLexer tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group result_processing
 */
class StringLexerTest extends \PHPUnit_Framework_TestCase
{
    public function testScannerRecognizesPointType()
    {
        $lexer = new StringLexer('POINT');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_POINT, $token['type']);
        $this->assertEquals('POINT', $token['value']);
    }

    public function testScannerRecognizesLineStringType()
    {
        $lexer = new StringLexer('LINESTRING');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_LINESTRING, $token['type']);
        $this->assertEquals('LINESTRING', $token['value']);
    }

    public function testScannerRecognizesPolygonType()
    {
        $lexer = new StringLexer('POLYGON');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_POLYGON, $token['type']);
        $this->assertEquals('POLYGON', $token['value']);
    }

    public function testScannerRecognizesMultiPointType()
    {
        $lexer = new StringLexer('MULTIPOINT');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_MULTIPOINT, $token['type']);
        $this->assertEquals('MULTIPOINT', $token['value']);
    }

    public function testScannerRecognizesMultiLineStringType()
    {
        $lexer = new StringLexer('MULTILINESTRING');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_MULTILINESTRING, $token['type']);
        $this->assertEquals('MULTILINESTRING', $token['value']);
    }

    public function testScannerRecognizesMultiPolygonType()
    {
        $lexer = new StringLexer('MULTIPOLYGON');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_MULTIPOLYGON, $token['type']);
        $this->assertEquals('MULTIPOLYGON', $token['value']);
    }

    public function testScannerRecognizesGeometryCollectionType()
    {
        $lexer = new StringLexer('GEOMETRYCOLLECTION');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_GEOMETRYCOLLECTION, $token['type']);
        $this->assertEquals('GEOMETRYCOLLECTION', $token['value']);
    }

    public function testScannerRecognizesPositiveInteger()
    {
        $lexer = new StringLexer('35');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_INTEGER, $token['type']);
        $this->assertEquals(35, $token['value']);
    }

    public function testScannerRecognizesNegativeInteger()
    {
        $lexer = new StringLexer('-25');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_INTEGER, $token['type']);
        $this->assertEquals(-25, $token['value']);
    }

    public function testScannerRecognizesPositiveFloat()
    {
        $lexer = new StringLexer('35.635');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_FLOAT, $token['type']);
        $this->assertEquals(35.635, $token['value']);
    }

    public function testScannerRecognizesNegativeFloat()
    {
        $lexer = new StringLexer('-120.33');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_FLOAT, $token['type']);
        $this->assertEquals(-120.33, $token['value']);
    }

    public function testScannerRecognizesSrid()
    {
        $lexer = new StringLexer('SRID');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(StringLexer::T_SRID, $token['type']);
        $this->assertEquals('SRID', $token['value']);
    }

    public function testScannerTokenizesGeometryValueCorrectly()
    {
        $value  = 'SRID=4326;LINESTRING(0 0.0, 10.1 -10.025, 20.5 25.9, 50 60)';
        $tokens = array(
            array(
                'value'    => 'SRID',
                'type'     => StringLexer::T_SRID,
                'position' => 0
            ),
            array(
                'value'    => '=',
                'type'     => StringLexer::T_EQUALS,
                'position' => 4
            ),
            array(
                'value'    => '4326',
                'type'     => StringLexer::T_INTEGER,
                'position' => 5
            ),
            array(
                'value'    => ';',
                'type'     => StringLexer::T_SEMICOLON,
                'position' => 9
            ),
            array(
                'value'    => 'LINESTRING',
                'type'     => StringLexer::T_LINESTRING,
                'position' => 10
            ),
            array(
                'value'    => '(',
                'type'     => StringLexer::T_OPEN_PARENTHESIS,
                'position' => 20
            ),
            array(
                'value'    => 0,
                'type'     => StringLexer::T_INTEGER,
                'position' => 21
            ),
            array(
                'value'    => 0,
                'type'     => StringLexer::T_FLOAT,
                'position' => 23
            ),
            array(
                'value'    => ',',
                'type'     => StringLexer::T_COMMA,
                'position' => 26
            ),
            array(
                'value'    => 10.1,
                'type'     => StringLexer::T_FLOAT,
                'position' => 28
            ),
            array(
                'value'    => -10.025,
                'type'     => StringLexer::T_FLOAT,
                'position' => 33
            ),
            array(
                'value'    => ',',
                'type'     => StringLexer::T_COMMA,
                'position' => 40
            ),
            array(
                'value'    => 20.5,
                'type'     => StringLexer::T_FLOAT,
                'position' => 42
            ),
            array(
                'value'    => 25.9,
                'type'     => StringLexer::T_FLOAT,
                'position' => 47
            ),
            array(
                'value'    => ',',
                'type'     => StringLexer::T_COMMA,
                'position' => 51
            ),
            array(
                'value'    => 50,
                'type'     => StringLexer::T_INTEGER,
                'position' => 53
            ),
            array(
                'value'    => 60,
                'type'     => StringLexer::T_INTEGER,
                'position' => 56
            ),
            array(
                'value'    => ')',
                'type'     => StringLexer::T_CLOSE_PARENTHESIS,
                'position' => 58
            )
        );

        $lexer = new StringLexer($value);

        foreach ($tokens as $expected) {
            $lexer->moveNext();

            $actual = $lexer->lookahead;

            $this->assertEquals($expected, $actual);
        }

        $this->assertFalse($lexer->moveNext());
    }
}
