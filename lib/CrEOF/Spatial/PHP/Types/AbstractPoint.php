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

namespace CrEOF\Spatial\PHP\Types;

use CrEOF\Geo\String\Exception\RangeException;
use CrEOF\Geo\String\Exception\UnexpectedValueException;
use CrEOF\Geo\String\Parser;
use CrEOF\Spatial\Exception\InvalidValueException;

/**
 * Abstract point object for POINT spatial types
 *
 * http://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
 * http://docs.geotools.org/latest/userguide/library/referencing/order.html
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractPoint extends AbstractGeometry
{
    /**
     * @var float $x
     */
    protected $x;

    /**
     * @var float $y
     */
    protected $y;

    public function __construct()
    {
        $argv = $this->validateArguments(func_get_args());

        call_user_func_array(array($this, 'construct'), $argv);
    }

    /**
     * @param mixed $x
     *
     * @return self
     * @throws InvalidValueException
     */
    public function setX($x)
    {
        $parser = new Parser($x);

        try {
            $this->x = (float) $parser->parse();
        } catch (RangeException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        } catch (UnexpectedValueException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param mixed $y
     *
     * @return self
     * @throws InvalidValueException
     */
    public function setY($y)
    {
        $parser = new Parser($y);

        try {
            $this->y = (float) $parser->parse();
        } catch (RangeException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        } catch (UnexpectedValueException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }


    /**
     * @param mixed $latitude
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        return $this->setY($latitude);
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->getY();
    }

    /**
     * @param mixed $longitude
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        return $this->setX($longitude);
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->getX();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::POINT;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array($this->x, $this->y);
    }

    /**
     * @param array $argv
     *
     * @return array
     * @throws InvalidValueException
     */
    protected function validateArguments(array $argv = null)
    {
        $argc = count($argv);

        if (1 == $argc && is_array($argv[0])) {
            return $argv[0];
        }

        if (2 == $argc) {
            if (is_array($argv[0]) && (is_numeric($argv[1]) || is_null($argv[1]) || is_string($argv[1]))) {
                $argv[0][] = $argv[1];

                return $argv[0];
            }

            if ((is_numeric($argv[0]) || is_string($argv[0])) && (is_numeric($argv[1]) || is_string($argv[1]))) {
                return $argv;
            }
        }

        if (3 == $argc) {
            if ((is_numeric($argv[0]) || is_string($argv[0])) && (is_numeric($argv[1]) || is_string($argv[1])) && (is_numeric($argv[2]) || is_null($argv[2]) || is_string($argv[2]))) {
                return $argv;
            }
        }

        array_walk($argv, function (&$value) {
            if (is_array($value)) {
                $value = 'Array';
            } else {
                $value = sprintf('"%s"', $value);
            }
        });

        throw new InvalidValueException(sprintf('Invalid parameters passed to %s::%s: %s', get_class($this), '__construct', implode(', ', $argv)));
    }

    /**
     * @param int      $x
     * @param int      $y
     * @param null|int $srid
     */
    protected function construct($x, $y, $srid = null)
    {
        $this->setX($x)
            ->setY($y)
            ->setSrid($srid);
    }
}
