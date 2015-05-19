<?php
/**
 * Copyright (C) 2012, 2014 Derek J. Lambert
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

namespace CrEOF\Spatial\DBAL\Types;

use CrEOF\Spatial\Exception\InvalidValueException;

/**
 * Reader for spatial WKB values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class BinaryReader
{
    const WKB_XDR = 0;
    const WKB_NDR = 1;

    /**
     * @var int
     */
    private $byteOrder;

    /**
     * @var string
     */
    private $input;

    /**
     * @param string $input
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    /**
     * @param string $format
     *
     * @return array
     */
    public function unpackInput($format)
    {
        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $code = 'a';
        } else {
            $code = 'A';
        }

        $result      = unpack(sprintf('%sresult/%s*input', $format, $code), $this->input);
        $this->input = $result['input'];

        return $result['result'];
    }

    /**
     * @return int
     */
    public function long()
    {
        $this->checkByteOrder();

        if (self::WKB_NDR === $this->byteOrder) {
            return $this->unpackInput('V');
        }

        return $this->unpackInput('N');
    }

    /**
     * @return float
     */
    public function double()
    {
        $this->checkByteOrder();

        $double = $this->unpackInput('d');

        if (self::WKB_NDR === $this->byteOrder) {
            return $double;
        }

        $double = unpack('dvalue', strrev(pack('d', $double)));

        return $double['value'];
    }


    /**
     * @return int
     * @throws InvalidValueException
     */
    public function byteOrder()
    {
        $byteOrder = $this->unpackInput('C');

        if ($byteOrder !== self::WKB_XDR && $byteOrder !== self::WKB_NDR) {
            throw InvalidValueException::invalidByteOrder($byteOrder);
        }

        return $this->byteOrder = $byteOrder;
    }

    /**
     * @param string $input
     */
    private function setInput($input)
    {
        $this->input = Utils::toBinary($input);
    }

    private function checkByteOrder()
    {
        if ( ! isset($this->byteOrder)) {
            throw InvalidValueException::invalidByteOrder('unset');
        }
    }
}
