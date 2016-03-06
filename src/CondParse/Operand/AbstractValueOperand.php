<?php


namespace CondParse\Operand;


use CondParse\OperandStack;

abstract class AbstractValueOperand extends AbstractOperand
{
    public function consumeTokens(OperandStack $operandStack)
    {
        return $this;
    }
}
