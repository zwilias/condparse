<?php


namespace CondParse\Operand;


abstract class AbstractValueOperand extends AbstractOperand
{
    function consumeTokens(\SplStack $operandStack)
    {
        return $this;
    }
}
