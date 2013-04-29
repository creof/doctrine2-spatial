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
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
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
     * @param GeometryInterface $object
     * @param string            $type
     * @param mixed             $value
     *
     * @return InvalidValueException
     */
    public static function invalidType(GeometryInterface $object, $type, $value)
    {
        return new self(sprintf('Invalid %s %s value of type "%s"', $object->getType(), $type, (is_object($value) ? get_class($value) : gettype($value))));
    }

    /**
     * @param string $ring
     *
     * @return InvalidValueException
     */
    public static function ringNotClosed($ring)
    {
        return new self(sprintf('Invalid polygon, ring "(%s)" is not closed', $ring));
    }

    /**
     * @param int $order
     *
     * @return InvalidValueException
     */
    public static function invalidByteOrder($order)
    {
        return new self(sprintf('Invalid byte order "%s"', $order));
    }

    /**
     * @param string $type
     *
     * @return InvalidValueException
     */
    public static function unsupportedWktType($type)
    {
        return self::unsupportedType('WKT', $type);
    }

    /**
     * @param int $type
     *
     * @return InvalidValueException
     */
    public static function unsupportedWkbType($type)
    {
        return self::unsupportedType('WKB', $type);
    }

    /**
     * @param string $type
     *
     * @return InvalidValueException
     */
    public static function unsupportedEwktType($type)
    {
        return self::unsupportedType('EWKT', $type);
    }

    /**
     * @param string $typeFamily
     * @param string $type
     *
     * @return InvalidValueException
     */
    public static function unsupportedType($typeFamily, $type)
    {
        return new self(sprintf('Unsupported %s type "%s".', $typeFamily, $type));
    }

    /**
     * @param string $message
     * @param string $input
     *
     * @return InvalidValueException
     */
    public static function syntaxError($message, $input)
    {
        return new self(sprintf('[Syntax Error] %s in value "%s"', $message, $input));
    }

    /**
     * @param int $srid
     *
     * @return InvalidValueException
     */
    public static function invalidSrid($srid)
    {
        return new self(sprintf('Invalid SRID "%d"', $srid));
    }

    /**
     * @param string $class
     * @param string $method
     * @param array  $parameters
     *
     * @return InvalidValueException
     */
    public static function invalidParameters($class, $method, array $parameters)
    {
        array_walk($parameters, function(&$value) {
                if (is_array($value)) {
                    $value = 'Array';
                } else {
                    $value = sprintf('"%s"', $value);
                }
        });

        return new self(sprintf('Invalid parameters passed to %s::%s: %s', $class, $method, implode(', ', $parameters)));
    }

    /**
     * @return InvalidValueException
     */
    public static function invalidValueNoGeometryInterface()
    {
        return new self('Geometry column values must implement GeometryInterface');
    }

    /**
     * @return InvalidValueException
     */
    public static function invalidValueNotGeography()
    {
        return new self('Geography columns require Geography values');
    }

    /**
     * @param mixed $value
     *
     * @return InvalidValueException
     */
    public static function invalidLatitude($value)
    {
        return new self(sprintf('Invalid latitude value "%s", must be in range -90 to 90.', $value));
    }

    /**
     * @param mixed $value
     *
     * @return InvalidValueException
     */
    public static function invalidLongitude($value)
    {
        return new self(sprintf('Invalid longitude value "%s", must be in range -180 to 180.', $value));
    }
}
