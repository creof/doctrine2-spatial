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

namespace CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql;

use CrEOF\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;
use CrEOF\Spatial\ORM\Query\AST\Functions\ReturnsGeometryInterface;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;

/**
 * ST_SnapToGrid DQL function.
 *
 * Possible signatures with 2, 3, 5 or 6 parameters:
 *  geometry ST_SnapToGrid(geometry geomA, float size);
 *  geometry ST_SnapToGrid(geometry geomA, float sizeX, float sizeY);
 *  geometry ST_SnapToGrid(geometry geomA, float originX, float originY, float sizeX, float sizeY);
 *  geometry ST_SnapToGrid(geometry geomA, geometry pointOrigin, float sizeX, float sizeY, float sizeZ, float sizeM);
 *
 * @author  Dragos Protung
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org
 */
class STSnapToGrid extends AbstractSpatialDQLFunction implements ReturnsGeometryInterface
{
    /**
     * Parse SQL.
     *
     * @param Parser $parser parser
     *
     * @throws QueryException Query exception
     */
    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        // 1st signature
        $this->addGeometryExpression($parser->ArithmeticFactor());
        $parser->match(Lexer::T_COMMA);
        $this->addGeometryExpression($parser->ArithmeticFactor());

        // 2nd signature
        if (Lexer::T_COMMA === $lexer->lookahead['type']) {
            $parser->match(Lexer::T_COMMA);
            $this->addGeometryExpression($parser->ArithmeticFactor());
        }

        // 3rd signature
        if (Lexer::T_COMMA === $lexer->lookahead['type']) {
            $parser->match(Lexer::T_COMMA);
            $this->addGeometryExpression($parser->ArithmeticFactor());

            $parser->match(Lexer::T_COMMA);
            $this->addGeometryExpression($parser->ArithmeticFactor());

            // 4th signature
            if (Lexer::T_COMMA === $lexer->lookahead['type']) {
                // sizeM
                $parser->match(Lexer::T_COMMA);
                $this->addGeometryExpression($parser->ArithmeticFactor());
            }
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Function SQL name getter.
     *
     * @since 2.0 This function replace the protected property functionName.
     *
     * @return string
     */
    protected function getFunctionName(): string
    {
        return 'ST_SnapToGrid';
    }

    /**
     * Maximum number of parameter for the spatial function.
     *
     * @since 2.0 This function replace the protected property maxGeomExpr.
     *
     * @return int The inherited methods shall NOT return null, but 0 when function has no parameter.
     */
    protected function getMaxParameter(): int
    {
        return 6;
    }

    /**
     * Minimum number of parameter for the spatial function.
     *
     * @since 2.0 This function replace the protected property minGeomExpr.
     *
     * @return int The inherited methods shall NOT return null, but 0 when function has no parameter.
     */
    protected function getMinParameter(): int
    {
        return 2;
    }

    /**
     * Get the platforms accepted.
     *
     * @since 2.0 This function replace the protected property platforms.
     *
     * @return string[] a non-empty array of accepted platforms.
     */
    protected function getPlatforms(): array
    {
        return ['postgresql'];
    }
}
