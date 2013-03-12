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

namespace CrEOF\Spatial\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Abstract DQL function requiring 3 parameters
 *
 * @author  Tom Vogt <tom@lemuria.org>
 * @license MIT
 */
abstract class AbstractTripleGeometryDQLFunction extends AbstractSingleGeometryDQLFunction
{
    /**
     * @var Node
     */
    protected $secondGeomExpression;

    /**
     * @var Node
     */
    protected $thirdGeomExpression;

    /**
     * @param SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $this->validatePlatform($sqlWalker->getConnection()->getDatabasePlatform());

        return sprintf(
            '%s(%s, %s, %s)',
            $this->functionName,
            $this->firstGeomExpression->dispatch($sqlWalker),
            $this->secondGeomExpression->dispatch($sqlWalker),
            $this->thirdGeomExpression->dispatch($sqlWalker)
        );
    }

    /**
     * @param Parser $parser
     */
    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->firstGeomExpression = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->secondGeomExpression = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->thirdGeomExpression = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
