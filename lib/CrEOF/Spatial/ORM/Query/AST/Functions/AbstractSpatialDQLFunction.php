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

namespace CrEOF\Spatial\ORM\Query\AST\Functions;

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Query\AST\ASTException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Abstract spatial DQL function.
 */
abstract class AbstractSpatialDQLFunction extends FunctionNode
{
    /**
     * @var string
     */
    protected $functionName;

    /**
     * @var Node[]
     */
    protected $geomExpr = [];

    /**
     * @var int
     */
    protected $maxGeomExpr;

    /**
     * @var int
     */
    protected $minGeomExpr;

    /**
     * @var array
     */
    protected $platforms = [];

    /**
     * Get the SQL.
     *
     * @param SqlWalker $sqlWalker the SQL Walker
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws DBALException                when an invalid platform was specified for this connection
     * @throws ASTException                 when node cannot dispatch SqlWalker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $this->validatePlatform($sqlWalker->getConnection()->getDatabasePlatform());

        $arguments = [];
        foreach ($this->geomExpr as $expression) {
            $arguments[] = $expression->dispatch($sqlWalker);
        }

        return sprintf('%s(%s)', $this->functionName, implode(', ', $arguments));
    }

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

        $this->geomExpr[] = $parser->ArithmeticPrimary();

        while (count($this->geomExpr) < $this->minGeomExpr
            || ((null === $this->maxGeomExpr || count($this->geomExpr) < $this->maxGeomExpr)
                && Lexer::T_CLOSE_PARENTHESIS != $lexer->lookahead['type'])
        ) {
            $parser->match(Lexer::T_COMMA);

            $this->geomExpr[] = $parser->ArithmeticPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Test that the platform supports spatial type.
     *
     * @param AbstractPlatform $platform database spatial
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function validatePlatform(AbstractPlatform $platform)
    {
        $platformName = $platform->getName();

        if (isset($this->platforms) && !in_array($platformName, $this->platforms)) {
            throw new UnsupportedPlatformException(
                sprintf('DBAL platform "%s" is not currently supported.', $platformName)
            );
        }
    }
}
