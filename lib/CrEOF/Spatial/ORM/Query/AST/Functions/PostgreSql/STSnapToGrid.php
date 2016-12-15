<?php
/**
 * Copyright (C) 2016 Derek J. Lambert
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

namespace CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql;

use CrEOF\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;
use CrEOF\Spatial\ORM\Query\AST\Functions\ReturnsGeometryInterface;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;

/**
 * ST_SnapToGrid DQL function.
 *
 * Possible signatures:
 *  geometry ST_SnapToGrid(geometry geomA, float size);
 *  geometry ST_SnapToGrid(geometry geomA, float sizeX, float sizeY);
 *  geometry ST_SnapToGrid(geometry geomA, float originX, float originY, float sizeX, float sizeY);
 *  geometry ST_SnapToGrid(geometry geomA, geometry pointOrigin, float sizeX, float sizeY, float sizeZ, float sizeM);
 *
 * @author  Dragos Protung
 * @license http://mit-license.org MIT
 */
class STSnapToGrid extends AbstractSpatialDQLFunction implements ReturnsGeometryInterface
{
    protected $platforms = array('postgresql');

    protected $functionName = 'ST_SnapToGrid';

    protected $minGeomExpr = 2;

    protected $maxGeomExpr = 6;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        // 1st signature
        $this->geomExpr[] = $parser->ArithmeticFactor();
        $parser->match(Lexer::T_COMMA);
        $this->geomExpr[] = $parser->ArithmeticFactor();

        // 2nd signature
        if ($lexer->lookahead['type'] === Lexer::T_COMMA) {
            $parser->match(Lexer::T_COMMA);
            $this->geomExpr[] = $parser->ArithmeticFactor();
        }

        // 3rd signature
        if ($lexer->lookahead['type'] === Lexer::T_COMMA) {
            $parser->match(Lexer::T_COMMA);
            $this->geomExpr[] = $parser->ArithmeticFactor();

            $parser->match(Lexer::T_COMMA);
            $this->geomExpr[] = $parser->ArithmeticFactor();

            // 4th signature
            if ($lexer->lookahead['type'] === Lexer::T_COMMA) {
                // sizeM
                $parser->match(Lexer::T_COMMA);
                $this->geomExpr[] = $parser->ArithmeticFactor();
            }
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
