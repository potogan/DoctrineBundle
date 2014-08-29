<?php
namespace Potogan\DoctrineBundle\Query\Functions\Mysql;

use 
	Doctrine\ORM\Query\AST\Functions\FunctionNode,
	Doctrine\ORM\Query\Parser,
	Doctrine\ORM\Query\Lexer,
	Doctrine\ORM\Query\SqlWalker
;

/**
 * FindInSetFunction ::= "FIND_IN_SET" "(" ArithmeticPrimary "," ArithmeticPrimary ")"
 */
class FindInSet extends FunctionNode {
	public $needle = null;
	public $haystack = null;

	public function parse(Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);
		$this->needle = $parser->ArithmeticPrimary();
		$parser->match(Lexer::T_COMMA);
		$this->haystack = $parser->ArithmeticPrimary();
		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}

	public function getSql(SqlWalker $sqlWalker)
	{
		return 'FIND_IN_SET(' .
			$this->needle->dispatch($sqlWalker) . ', ' .
			$this->haystack->dispatch($sqlWalker) .
		')';
	}
}