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

use CrEOF\Spatial\DBAL\Types\Lexer;

/**
 * Lexer tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group common
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testScannerRecognizesPointType()
    {
        $lexer = new Lexer('POINT');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_POINT, $token['type']);
        $this->assertEquals('POINT', $token['value']);
    }

    public function testScannerRecognizesLineStringType()
    {
        $lexer = new Lexer('LINESTRING');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_LINESTRING, $token['type']);
        $this->assertEquals('LINESTRING', $token['value']);
    }

    public function testScannerRecognizesPolygonType()
    {
        $lexer = new Lexer('POLYGON');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_POLYGON, $token['type']);
        $this->assertEquals('POLYGON', $token['value']);
    }

    public function testScannerRecognizesMultiPointType()
    {
        $lexer = new Lexer('MULTIPOINT');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_MULTIPOINT, $token['type']);
        $this->assertEquals('MULTIPOINT', $token['value']);
    }

    public function testScannerRecognizesMultiLineStringType()
    {
        $lexer = new Lexer('MULTILINESTRING');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_MULTILINESTRING, $token['type']);
        $this->assertEquals('MULTILINESTRING', $token['value']);
    }

    public function testScannerRecognizesMultiPolygonType()
    {
        $lexer = new Lexer('MULTIPOLYGON');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_MULTIPOLYGON, $token['type']);
        $this->assertEquals('MULTIPOLYGON', $token['value']);
    }

    public function testScannerRecognizesGeometryCollectionType()
    {
        $lexer = new Lexer('GEOMETRYCOLLECTION');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_GEOMETRYCOLLECTION, $token['type']);
        $this->assertEquals('GEOMETRYCOLLECTION', $token['value']);
    }

    public function testScannerRecognizesPositiveInteger()
    {
        $lexer = new Lexer('35');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_INTEGER, $token['type']);
        $this->assertEquals(35, $token['value']);
    }

    public function testScannerRecognizesNegativeInteger()
    {
        $lexer = new Lexer('-25');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_INTEGER, $token['type']);
        $this->assertEquals(-25, $token['value']);
    }

    public function testScannerRecognizesPositiveFloat()
    {
        $lexer = new Lexer('35.635');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_FLOAT, $token['type']);
        $this->assertEquals(35.635, $token['value']);
    }

    public function testScannerRecognizesNegativeFloat()
    {
        $lexer = new Lexer('-120.33');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_FLOAT, $token['type']);
        $this->assertEquals(-120.33, $token['value']);
    }

    public function testScannerRecognizesSrid()
    {
        $lexer = new Lexer('SRID');

        $lexer->moveNext();

        $token = $lexer->lookahead;

        $this->assertEquals(Lexer::T_SRID, $token['type']);
        $this->assertEquals('SRID', $token['value']);
    }

    public function testScannerTokenizesGeometryValueCorrectly()
    {
        $value  = 'SRID=4326;LINESTRING(0 0.0, 10.1 -10.025, 20.5 25.9, 50 60)';
        $tokens = array(
            array(
                'value'    => 'SRID',
                'type'     => Lexer::T_SRID,
                'position' => 0
            ),
            array(
                'value'    => '=',
                'type'     => Lexer::T_EQUALS,
                'position' => 4
            ),
            array(
                'value'    => '4326',
                'type'     => Lexer::T_INTEGER,
                'position' => 5
            ),
            array(
                'value'    => ';',
                'type'     => Lexer::T_SEMICOLON,
                'position' => 9
            ),
            array(
                'value'    => 'LINESTRING',
                'type'     => Lexer::T_LINESTRING,
                'position' => 10
            ),
            array(
                'value'    => '(',
                'type'     => Lexer::T_OPEN_PARENTHESIS,
                'position' => 20
            ),
            array(
                'value'    => 0,
                'type'     => Lexer::T_INTEGER,
                'position' => 21
            ),
            array(
                'value'    => 0,
                'type'     => Lexer::T_FLOAT,
                'position' => 23
            ),
            array(
                'value'    => ',',
                'type'     => Lexer::T_COMMA,
                'position' => 26
            ),
            array(
                'value'    => 10.1,
                'type'     => Lexer::T_FLOAT,
                'position' => 28
            ),
            array(
                'value'    => -10.025,
                'type'     => Lexer::T_FLOAT,
                'position' => 33
            ),
            array(
                'value'    => ',',
                'type'     => Lexer::T_COMMA,
                'position' => 40
            ),
            array(
                'value'    => 20.5,
                'type'     => Lexer::T_FLOAT,
                'position' => 42
            ),
            array(
                'value'    => 25.9,
                'type'     => Lexer::T_FLOAT,
                'position' => 47
            ),
            array(
                'value'    => ',',
                'type'     => Lexer::T_COMMA,
                'position' => 51
            ),
            array(
                'value'    => 50,
                'type'     => Lexer::T_INTEGER,
                'position' => 53
            ),
            array(
                'value'    => 60,
                'type'     => Lexer::T_INTEGER,
                'position' => 56
            ),
            array(
                'value'    => ')',
                'type'     => Lexer::T_CLOSE_PARENTHESIS,
                'position' => 58
            )
        );

        $lexer = new Lexer($value);

        foreach ($tokens as $expected) {
            $lexer->moveNext();

            $actual = $lexer->lookahead;

            $this->assertEquals($expected, $actual);
        }

        $this->assertFalse($lexer->moveNext());
    }
}
