<?php

namespace CTF\CommonBundle\DBAL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
 
/**
 * Implements the SUBSTRING_INDEX function in mysql
 */
class SubstringIndex extends FunctionNode {
    private $string;
    private $delimiter;
    private $count;
 
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker) {
        return 'SUBSTRING_INDEX(' . $this->string->dispatch($sqlWalker) . ',' . $this->delimiter->dispatch($sqlWalker) . ',' . $this->count->dispatch($sqlWalker) . ')';
    }
 
    public function parse(\Doctrine\ORM\Query\Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->string = $parser->StringExpression();
        $parser->match(Lexer::T_COMMA);
        $this->delimiter = $parser->StringExpression();
        $parser->match(Lexer::T_COMMA);
        $this->count = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
 
}
