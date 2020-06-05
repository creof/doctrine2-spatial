<?php
/**
 * Copyright (C) 2020 Alexandre Tranchant
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
 * Abstract point object for POINT spatial types.
 *
 * https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
 * https://docs.geotools.org/latest/userguide/library/referencing/order.html
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
abstract class AbstractPoint extends AbstractGeometry
{
    /**
     * The longitude.
     *
     * @var float
     */
    protected $x;

    /**
     * The Latitude.
     *
     * @var float
     */
    protected $y;

    /**
     * AbstractPoint constructor.
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct()
    {
        $argv = $this->validateArguments(func_get_args());

        call_user_func_array([$this, 'construct'], $argv);
    }

    /**
     * Latitude getter.
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->getY();
    }

    /**
     * Longitude getter.
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->getX();
    }

    /**
     * Type getter.
     *
     * @return string Point
     */
    public function getType()
    {
        return self::POINT;
    }

    /**
     * X getter. (Longitude getter).
     *
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Y getter. Latitude getter.
     *
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Latitude fluent setter.
     *
     * @param mixed $latitude the new latitude of point
     *
     * @throws InvalidValueException when latitude is not valid
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        return $this->setY($latitude);
    }

    /**
     * Longitude setter.
     *
     * @param mixed $longitude the new longitude
     *
     * @throws InvalidValueException when longitude is not valid
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        return $this->setX($longitude);
    }

    /**
     * X setter. (Latitude setter).
     *
     * @param mixed $x the new X
     *
     * @throws InvalidValueException when x is not valid
     *
     * @return self
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
     * Y setter. Longitude Setter.
     *
     * @param mixed $y the new Y value
     *
     * @throws InvalidValueException when Y is invalid, not in valid range
     *
     * @return self
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
     * Convert point into an array X, Y.
     * Latitude, longitude.
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->x, $this->y];
    }

    /**
     * Abstract point constructor.
     *
     * @param int      $x    X, latitude
     * @param int      $y    Y, longitude
     * @param int|null $srid Spatial Reference System Identifier
     *
     * @throws InvalidValueException if x or y are invalid
     */
    protected function construct($x, $y, $srid = null)
    {
        $this->setX($x)
            ->setY($y)
            ->setSrid($srid)
        ;
    }

    /**
     * Validate arguments.
     *
     * @param array $argv list of arguments
     *
     * @throws InvalidValueException when an argument is not valid
     */
    protected function validateArguments(array $argv = null): array
    {
        $argc = count($argv);

        switch ($argc) {
            case 1:
                return $this->checkOneArgument($argv);
            case 2:
                return $this->checkTwoArguments($argv);
            case 3:
                return $this->checkThreeArguments($argv);
            default:
                throw $this->exception($argv);
        }
    }

    /**
     * Check and the argv argument.
     *
     * @param array|null $argv the argument which should be an array
     *
     * @throws InvalidValueException when argv is not an array
     */
    private function checkOneArgument(?array $argv): array
    {
        if (is_array($argv[0])) {
            return $argv[0];
        }

        throw $this->exception($argv);
    }

    /**
     * Check and the argv argument which have three elements.
     *
     * @param array|null $argv the argument which should be an array
     *
     * @throws InvalidValueException when argv is not an array
     */
    private function checkThreeArguments(?array $argv): array
    {
        if ($this->isNumericOrString($argv[0])
            && $this->isNumericOrString($argv[1])
            && $this->isNumericOrStringOrNull($argv[2])
        ) {
            return $argv;
        }

        throw $this->exception($argv);
    }

    /**
     * Check and the argv argument which have two elements.
     *
     * @param array|null $argv the argument which should be an array
     *
     * @throws InvalidValueException when argv is not an array
     */
    private function checkTwoArguments(?array $argv): array
    {
        if (is_array($argv[0]) && (is_numeric($argv[1]) || null === $argv[1] || is_string($argv[1]))) {
            $argv[0][] = $argv[1];

            return $argv[0];
        }

        if ((is_numeric($argv[0]) || is_string($argv[0])) && (is_numeric($argv[1]) || is_string($argv[1]))) {
            return $argv;
        }

        throw $this->exception($argv);
    }

    /**
     * Create a new InvalidException.
     *
     * @param array|null $argv the argv is read to compute message of exception
     */
    private function exception(?array $argv): InvalidValueException
    {
        array_walk($argv, function (&$value) {
            if (is_array($value)) {
                $value = 'Array';
            } else {
                $value = sprintf('"%s"', $value);
            }
        });

        return new InvalidValueException(sprintf(
            'Invalid parameters passed to %s::%s: %s',
            get_class($this),
            '__construct',
            implode(', ', $argv)
        ));
    }

    /**
     * Is parameter numeric or string?
     *
     * @param mixed $parameter to test
     */
    private function isNumericOrString($parameter): bool
    {
        return is_numeric($parameter) || is_string($parameter);
    }

    /**
     * Is parameter numeric or string or null?
     *
     * @param mixed $parameter to test
     */
    private function isNumericOrStringOrNull($parameter): bool
    {
        return is_numeric($parameter) || is_string($parameter) || null === ($parameter);
    }
}
