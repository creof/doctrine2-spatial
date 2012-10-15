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

namespace CrEOF\Spatial\DBAL\Types;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geometry;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

/**
 * Parse spatial WKT values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class WktValueParser
{
    /**
     * @var array
     */
    private $stack;

    /**
     * @var array
     */
    private $current;

    /**
     * @var array
     */
    private $point;

    /**
     * @var int
     */
    private $marker;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $level;

    /**
     * @param string $string
     *
     * @return Geometry
     */
    public function parse($string)
    {
        if ( ! $string) {
            return array();
        }

        $this->stack   = array();
        $this->point   = array();
        $this->current = array();
        $this->level   = 0;
        $pattern       = '/^(?<type>\w+)\((?<value>[0-9,-\.\(\)\s]+)\)$/';

        preg_match($pattern, $string, $matches);

        $this->type  = strtolower($matches['type']);
        $this->value = $matches['value'];

        for ($this->position = 0; $this->position < strlen($this->value); $this->position++) {
            switch ($char = $this->value[$this->position]) {
                case '(':
                    $this->pushLevel();
                    break;
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                case '0':
                case '.':
                case '-':
                    if ($this->marker === null) {
                        $this->marker = $this->position;
                    }
                    break;
                case ' ':
                    $this->pushNumber();
                    break;
                case ',':
                    $this->pushPoint();
                    break;
                case ')':
                    $this->popLevel();
                    break;
            }
        }

        $this->pushPoint();

        return $this->finalize();
    }

    private function pushNumber()
    {
        if ($this->marker !== null) {
            $this->point[] = substr($this->value, $this->marker, $this->position - $this->marker);;
            $this->marker  = null;
        }
    }

    private function pushPoint()
    {
        if ($this->marker !== null) {
            $this->pushNumber();

            $this->current[] = new Point($this->point[0], $this->point[1]);
            $this->point     = array();
        }
    }

    private function pushLevel()
    {
        $this->level++;
        $this->stack[] = $this->current;
        $this->current = array();
    }

    private function popLevel()
    {
        $this->pushPoint();

        $t = array_pop($this->stack);

        switch ($this->level--) {
            case 0:
                $t[] = $this->current;
                break;
            case 1:
                $t[] = new LineString($this->current);
                break;
        }

        $this->current = $t;
    }

    private function finalize()
    {
        switch ($this->type) {
            case Geometry::POINT:
                return $this->current[0];
                break;
            case Geometry::LINESTRING:
                return new LineString($this->current);
                break;
            case Geometry::POLYGON:
                return new Polygon($this->current);
                break;
            default:
                throw InvalidValueException::unsupportedWktType($this->type);
                break;
        }
    }
}
