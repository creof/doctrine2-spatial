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

/**
 * Parse spatial text values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class StringParser
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    private $input;

    /**
     * @var int
     */
    private $srid;

    /**
     * @var StringLexer
     */
    private $lexer;

    /**
     * @param string $input
     */
    public function __construct($input)
    {
        $this->input = $input;
        $this->lexer = new StringLexer($input);
    }

    /**
     * @return array
     */
    public function parse()
    {
        $this->lexer->moveNext();

        if ($this->lexer->lookahead['type'] == StringLexer::T_SRID) {
            $this->srid = $this->srid();
        }

        $geometry         = $this->geometry();
        $geometry['srid'] = $this->srid;

        return $geometry;
    }

    /**
     * @return int
     */
    protected function srid()
    {
        $this->match(StringLexer::T_SRID);
        $this->match(StringLexer::T_EQUALS);
        $this->match(StringLexer::T_INTEGER);

        $srid = $this->lexer->token['value'];

        $this->match(StringLexer::T_SEMICOLON);

        return $srid;
    }

    /**
     * @return string
     */
    protected function type()
    {
        $this->match(StringLexer::T_TYPE);

        return $this->lexer->token['value'];
    }

    /**
     * @return array
     */
    protected function point()
    {
        $x = $this->coordinate();
        $y = $this->coordinate();

        return array($x, $y);
    }

    /**
     * @return int|float
     */
    protected function coordinate()
    {
        $this->match(($this->lexer->isNextToken(StringLexer::T_FLOAT) ? StringLexer::T_FLOAT : StringLexer::T_INTEGER));

        return $this->lexer->token['value'];
    }

    protected function lineString()
    {
        return $this->pointList();
    }

    protected function polygon()
    {
        return $this->pointLists();
    }

    /**
     * @return array[]
     */
    protected function pointList()
    {
        $points = array($this->point());

        while ($this->lexer->isNextToken(StringLexer::T_COMMA)) {
            $this->match(StringLexer::T_COMMA);

            $points[] = $this->point();
        }

        return $points;
    }

    /**
     * @return array[]
     */
    protected function pointLists()
    {
        $this->match(StringLexer::T_OPEN_PARENTHESIS);

        $pointLists = array($this->pointList());

        $this->match(StringLexer::T_CLOSE_PARENTHESIS);

        while ($this->lexer->isNextToken(StringLexer::T_COMMA)) {
            $this->match(StringLexer::T_COMMA);
            $this->match(StringLexer::T_OPEN_PARENTHESIS);

            $pointLists[] = $this->pointList();

            $this->match(StringLexer::T_CLOSE_PARENTHESIS);
        }

        return $pointLists;
    }

    /**
     * @return array[]
     */
    protected function multiPolygon()
    {
        $this->match(StringLexer::T_OPEN_PARENTHESIS);

        $polygons = array($this->polygon());

        $this->match(StringLexer::T_CLOSE_PARENTHESIS);

        while ($this->lexer->isNextToken(StringLexer::T_COMMA)) {
            $this->match(StringLexer::T_COMMA);
            $this->match(StringLexer::T_OPEN_PARENTHESIS);

            $polygons[] = $this->polygon();

            $this->match(StringLexer::T_CLOSE_PARENTHESIS);
        }

        return $polygons;
    }

    /**
     * @return array[]
     */
    protected function multiPoint()
    {
        return $this->pointList();
    }

    /**
     * @return array[]
     */
    protected function multiLineString()
    {
      return $this->pointLists();
    }

    /**
     * @return array
     */
    protected function geometry()
    {
        $type = $this->type();

        $this->match(StringLexer::T_OPEN_PARENTHESIS);

        $value = $this->$type();

        $this->match(StringLexer::T_CLOSE_PARENTHESIS);

        return array(
            'type'  => $type,
            'value' => $value
        );
    }

    /**
     * @return array[]
     */
    protected function geometryCollection()
    {
        $collection = array($this->geometry());

        while ($this->lexer->isNextToken(StringLexer::T_COMMA)) {
            $this->match(StringLexer::T_COMMA);

            $collection[] = $this->geometry();
        }

        return $collection;
    }

    protected function match($token)
    {
        $lookaheadType = $this->lexer->lookahead['type'];

        if ($lookaheadType !== $token && ($token !== StringLexer::T_TYPE || $lookaheadType <= StringLexer::T_TYPE)) {
            $this->syntaxError($this->lexer->getLiteral($token));
        }

        $this->lexer->moveNext();
    }

    /**
     * @param string $expected
     * @param array  $token
     *
     * @throws InvalidValueException
     */
    protected function syntaxError($expected = '', $token = null)
    {
        if ($token === null) {
            $token = $this->lexer->lookahead;
        }

        $tokenPos = (isset($token['position'])) ? $token['position'] : '-1';

        $message  = 'line 0, col ' . $tokenPos . ': Error: ';
        $message .= ($expected !== '') ? 'Expected ' . $expected . ', got ' : 'Unexpected ';
        $message .= ($this->lexer->lookahead === null) ? 'end of string.' : '"' . $token['value'] . '"';

        throw InvalidValueException::syntaxError($message, $this->input);
    }
}
