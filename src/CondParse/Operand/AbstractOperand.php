<?php


namespace CondParse\Operand;


use CondParse\LexerToken;
use CondParse\OperandInterface;

abstract class AbstractOperand implements OperandInterface
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $value;

    /**
     * @param LexerToken $lexerToken
     */
    public function __construct(LexerToken $lexerToken)
    {
        $this->token = $lexerToken->getToken();
        $this->value = $lexerToken->getValue();
    }
}
