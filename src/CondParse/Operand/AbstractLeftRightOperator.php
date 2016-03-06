<?php


namespace CondParse\Operand;


use CondParse\OperandInterface;
use CondParse\OperandStack;

abstract class AbstractLeftRightOperator extends AbstractOperand
{
    /** @var OperandInterface */
    protected $leftOperand;
    /** @var OperandInterface */
    protected $rightOperand;

    /**
     * @param OperandStack $operandStack
     * @return $this
     */
    public function consumeTokens(OperandStack $operandStack)
    {
        $this->rightOperand = $operandStack->pop();
        $this->leftOperand = $operandStack->pop();

        return $this;
    }
}
