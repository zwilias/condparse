<?php


namespace CondParse\Operand;


use CondParse\OperandInterface;

abstract class AbstractOperand implements OperandInterface
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $value;

    /**
     * @param string $token
     * @param mixed $value
     */
    public function __construct($token, $value)
    {
        $this->token = $token;
        $this->value = $value;
    }
}
