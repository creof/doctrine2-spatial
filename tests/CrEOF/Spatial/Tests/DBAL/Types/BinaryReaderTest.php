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

use CrEOF\Spatial\DBAL\Types\BinaryReader;

/**
 * BinaryReader tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @group result_processing
 */
class BinaryReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingBinaryByteOrder()
    {
        $value  = '01';
        $value  = pack('H*', $value);
        $reader = new BinaryReader($value);
        $result = $reader->byteOrder();

        $this->assertEquals(1, $result);
    }

    public function testReadingHexByteOrder()
    {
        $value  = '01';
        $reader = new BinaryReader($value);
        $result = $reader->byteOrder();

        $this->assertEquals(1, $result);
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid byte order "unset"
     */
    public function testReadingBinaryWithoutByteOrder()
    {
        $value  = '0101000000';
        $value  = pack('H*', $value);
        $reader = new BinaryReader($value);

        $reader->long();
    }

    /**
     * @expectedException        \CrEOF\Spatial\Exception\InvalidValueException
     * @expectedExceptionMessage Invalid byte order "unset"
     */
    public function testReadingHexWithoutByteOrder()
    {
        $value  = '0101000000';
        $reader = new BinaryReader($value);

        $reader->long();
    }

    public function testReadingNDRBinaryLong()
    {
        $value  = '0101000000';
        $value  = pack('H*', $value);
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->long();

        $this->assertEquals(1, $result);
    }

    public function testReadingXDRBinaryLong()
    {
        $value  = '0000000001';
        $value  = pack('H*', $value);
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->long();

        $this->assertEquals(1, $result);
    }

    public function testReadingNDRHexLong()
    {
        $value  = '0101000000';
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->long();

        $this->assertEquals(1, $result);
    }

    public function testReadingXDRHexLong()
    {
        $value  = '0000000001';
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->long();

        $this->assertEquals(1, $result);
    }

    public function testReadingNDRBinaryDouble()
    {
        $value  = '013D0AD7A3701D4140';
        $value  = pack('H*', $value);
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->double();

        $this->assertEquals(34.23, $result);
    }

    public function testReadingXDRBinaryDouble()
    {
        $value  = '0040411D70A3D70A3D';
        $value  = pack('H*', $value);
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->double();

        $this->assertEquals(34.23, $result);
    }

    public function testReadingNDRHexDouble()
    {
        $value  = '013D0AD7A3701D4140';
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->double();

        $this->assertEquals(34.23, $result);
    }

    public function testReadingXDRHexDouble()
    {
        $value  = '0040411D70A3D70A3D';
        $reader = new BinaryReader($value);

        $reader->byteOrder();

        $result = $reader->double();

        $this->assertEquals(34.23, $result);
    }
}
