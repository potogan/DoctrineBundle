<?php
namespace Potogan\DoctrineBundle\Query\Functions\Mysql;

use 
	Doctrine\ORM\Query\AST\Functions\FunctionNode,
	Doctrine\ORM\Query\Parser,
	Doctrine\ORM\Query\Lexer,
	Doctrine\ORM\Query\SqlWalker
;

/**
 * IfFunction ::= "IF" "(" ConditionalExpression "," ArithmeticPrimary "," ArithmeticPrimary ")"
 */
class MysqlIf extends FunctionNode {
	public $firstParameter = null;
	public $secondParameter = null;
	public $thirdParameter = null;

	public function parse(Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);
		$this->firstParameter = $parser->ConditionalExpression();
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