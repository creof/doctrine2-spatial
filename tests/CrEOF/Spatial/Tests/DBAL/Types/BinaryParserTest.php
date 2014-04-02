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

use CrEOF\Spatial\DBAL\Types\BinaryParser;

/**
 * BinaryParser tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group result_processing
 */
class BinaryParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid byte order "3"
     */
    public function testParsingBadByteOrder()
    {
        $value    = '03010000003D0AD7A3701D41400000000000C055C0';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);

        $parser->parse();
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Unsupported WKB type "21"
     */
    public function testParsingBadType()
    {
        $value    = '01150000003D0AD7A3701D41400000000000C055C0';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);

        $parser->parse();
    }

    public function testParsingNDRPointValue()
    {
        $value    = '01010000003D0AD7A3701D41400000000000C055C0';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingXDRPointValue()
    {
        $value    = '000000000140411D70A3D70A3DC055C00000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingNDRPointValueWithSrid()
    {
        $value    = '0101000020E61000003D0AD7A3701D41400000000000C055C0';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingXDRPointValueWithSrid()
    {
        $value    = '0020000001000010E640411D70A3D70A3DC055C00000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
        $expected = array(
            'srid'  => 4326,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException        \PHPUnit_Framework_Error
     * @expectedExceptionMessage unpack(): Type d: not enough input, need 8, have 4
     */
    public function testParsingShortNDRPointValue()
    {
        $value    = '01010000003D0AD7A3701D414000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    public function testParsingNDRLineStringValue()
    {
        $value    = '0102000000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRLineStringValue()
    {
        $value    = '00000000020000000240411D70A3D70A3DC055C000000000004046A66666666666C057000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRLineStringValueWithSrid()
    {
        $value    = '0102000020E6100000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRLineStringValueWithSrid()
    {
        $value    = '0020000002000010E60000000240411D70A3D70A3DC055C000000000004046A66666666666C057000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRPolygonValue()
    {
        $value    = '010300000001000000050000000000000000000000000000000000000000000000000024400000000000000000000000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRPolygonValue()
    {
        $value    = '000000000300000001000000050000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRPolygonValueWithSrid()
    {
        $value    = '0103000020E610000001000000050000000000000000000000000000000000000000000000000024400000000000000000000000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRPolygonValueWithSrid()
    {
        $value    = '0020000003000010E600000001000000050000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiRingPolygonValue()
    {
        $value    = '01030000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C4000000000000014400000000000001440';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiRingPolygonValue()
    {
        $value    = '0000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiRingPolygonValueWithSrid()
    {
        $value    = '0103000020E61000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C4000000000000014400000000000001440';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiRingPolygonValueWithSrid()
    {
        $value    = '0020000003000010E6000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiPointValue()
    {
        $value    = '010400000004000000010100000000000000000000000000000000000000010100000000000000000024400000000000000000010100000000000000000024400000000000002440010100000000000000000000000000000000002440';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiPointValue()
    {
        $value    = '000000000400000004000000000100000000000000000000000000000000000000000140240000000000000000000000000000000000000140240000000000004024000000000000000000000100000000000000004024000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiPointValueWithSrid()
    {
        $value    = '0104000020E610000004000000010100000000000000000000000000000000000000010100000000000000000024400000000000000000010100000000000000000024400000000000002440010100000000000000000000000000000000002440';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiPointValueWithSrid()
    {
        $value    = '0020000004000010E600000004000000000100000000000000000000000000000000000000000140240000000000000000000000000000000000000140240000000000004024000000000000000000000100000000000000004024000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiLineStringValue()
    {
        $value    = '01050000000200000001020000000400000000000000000000000000000000000000000000000000244000000000000000000000000000002440000000000000244000000000000000000000000000002440010200000004000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C40';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiLineStringValue()
    {
        $value    = '0000000005000000020000000002000000040000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000020000000440140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiLineStringValueWithSrid()
    {
        $value    = '0105000020E61000000200000001020000000400000000000000000000000000000000000000000000000000244000000000000000000000000000002440000000000000244000000000000000000000000000002440010200000004000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C40';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiLineStringValueWithSrid()
    {
        $value    = '0020000005000010E6000000020000000002000000040000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000020000000440140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiPolygonValue()
    {
        $value    = '01060000000200000001030000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C400000000000001440000000000000144001030000000100000005000000000000000000F03F000000000000F03F0000000000000840000000000000F03F00000000000008400000000000000840000000000000F03F0000000000000840000000000000F03F000000000000F03F';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiPolygonValue()
    {
        $value    = '0000000006000000020000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000000000000300000001000000053FF00000000000003FF000000000000040080000000000003FF0000000000000400800000000000040080000000000003FF000000000000040080000000000003FF00000000000003FF0000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRMultiPolygonValueWithSrid()
    {
        $value    = '0106000020E61000000200000001030000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C400000000000001440000000000000144001030000000100000005000000000000000000F03F000000000000F03F0000000000000840000000000000F03F00000000000008400000000000000840000000000000F03F0000000000000840000000000000F03F000000000000F03F';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRMultiPolygonValueWithSrid()
    {
        $value    = '0020000006000010E6000000020000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000000000000300000001000000053FF00000000000003FF000000000000040080000000000003FF0000000000000400800000000000040080000000000003FF000000000000040080000000000003FF00000000000003FF0000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRGeometryCollectionValue()
    {
        $value    = '01070000000300000001010000000000000000002440000000000000244001010000000000000000003E400000000000003E400102000000020000000000000000002E400000000000002E4000000000000034400000000000003440';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRGeometryCollectionValue()
    {
        $value    = '0000000007000000030000000001402400000000000040240000000000000000000001403E000000000000403E000000000000000000000200000002402E000000000000402E00000000000040340000000000004034000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingNDRGeometryCollectionValueWithSrid()
    {
        $value    = '0107000020E61000000300000001010000000000000000002440000000000000244001010000000000000000003E400000000000003E400102000000020000000000000000002E400000000000002E4000000000000034400000000000003440';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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

    public function testParsingXDRGeometryCollectionValueWithSrid()
    {
        $value    = '0020000007000010E6000000030000000001402400000000000040240000000000000000000001403E000000000000403E000000000000000000000200000002402E000000000000402E00000000000040340000000000004034000000000000';
        $value    = pack('H*', $value);
        $parser   = new BinaryParser($value);
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
}
