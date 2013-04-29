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

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Abstract spatial DQL function
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractSpatialDQLFunction extends FunctionNode
{
    /**
     * @var string
     */
    protected $functionName;

    /**
     * @var array
     */
    protected $platforms = array();

    /**
     * @var Node[]
     */
    protected $geomExpr = array();

    /**
     * @var int
     */
    protected $minGeomExpr;

    /**
     * @var int
     */
    protected $maxGeomExpr;

    /**
     * @param Parser $parser
     */
    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->geomExpr[] = $parser->ArithmeticPrimary();

        while (count($this->geomExpr) < $this->minGeomExpr || (($this->maxGeomExpr === null || count($this->geomExpr) < $this->maxGeomExpr) && $lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS)) {
            $parser->match(Lexer::T_COMMA);

            $this->geomExpr[] = $parser->ArithmeticPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $this->validatePlatform($sqlWalker->getConnection()->getDatabasePlatform());

        $result = sprintf(
            '%s(%s',
            $this->functionName,
            $this->geomExpr[0]->dispatch($sqlWalker)
        );

        for ($i = 1, $size = count($this->geomExpr); $i < $size; $i++) {
            $result .= ', ' . $this->geomExpr[$i]->dispatch($sqlWalker);
        }

        $result .= ')';

        return $result;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @throws UnsupportedPlatformException
     */
    protected function validatePlatform(AbstractPlatform $platform)
    {
        $platformName = $platform->getName();

        if (isset($this->platforms) && ! in_array($platformName, $this->platforms)) {
            throw UnsupportedPlatformException::unsupportedPlatform($platformName);
        }
    }
}
