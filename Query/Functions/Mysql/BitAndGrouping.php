<?php

namespace Potogan\DoctrineBundle\Query\Functions\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;

/**
 * BitAndGroupingFunction ::= "GROUP_AND_OR" "(" ArithmeticPrimary ")"
 */
class BitAndGrouping extends FunctionNode
{
    public $needle = null;
    public $haystack = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->needle = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'BIT_AND(' . $this->needle->dispatch($sqlWalker) . ')';
    }
}
