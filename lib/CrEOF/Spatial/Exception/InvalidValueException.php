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

namespace CrEOF\Spatial\Exception;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Exception;

/**
 * InvalidValueException class
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class InvalidValueException extends Exception
{
    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return InvalidValueException
     */
    static public function invalidType($type, $value)
    {
        return new self(sprintf('Value needs to be of type "%s", is "%s".', $type, (is_object($value) ? get_class($value) : gettype($value))));
    }

    /**
     * @param LineString $ring
     *
     * @return InvalidValueException
     */
    static public function ringNotClosed(LineString $ring)
    {
        return new self(sprintf('Ring "%s" is not closed.', $ring));
    }

    /**
     * @param int $order
     *
     * @return InvalidValueException
     */
    static public function invalidByteOrder($order)
    {
        return new self(sprintf('Invalid byte order "%d".', $order));
    }

    /**
     * @param string $type
     *
     * @return InvalidValueException
     */
    static public function unsupportedWktType($type)
    {
        return new self(sprintf('Unsupported WKT type "%s".', $type));
    }

    /**
     * @param int $type
     *
     * @return InvalidValueException
     */
    static public function unsupportedWkbType($type)
    {
        return new self(sprintf('Unsupported WKB type "%d".', $type));
    }
}
