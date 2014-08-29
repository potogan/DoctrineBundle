<?php

namespace Potogan\DoctrineBundle\Query\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;

/**
 * IfFunction ::= "IF" "(" ArithmeticPrimary "," ArithmeticPrimary "," ArithmeticPrimary ")"
 */
class MysqlIf extends FunctionNode
{
    public $firstParameter = null;
    public $secondParameter = null;
    public $thirdParameter = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstParameter = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondParameter = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->thirdParameter = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'IF(' .
            $this->firstParameter->dispatch($sqlWalker) . ', ' .
            $this->secondParameter->dispatch($sqlWalker) . ', ' .
            $this->thirdParameter->dispatch($sqlWalker) .
        ')';
    }
}
