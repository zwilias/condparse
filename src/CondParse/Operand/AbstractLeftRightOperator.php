<?php


namespace CondParse\Operand;


use CondParse\OperandInterface;

abstract class AbstractLeftRightOperator extends AbstractOperand
{
    /** @var OperandInterface */
    protected $leftOperand;
    /** @var OperandInterface */
    protected $rightOperand;

    /**
     * @param \SplStack $operandStack
     * @return $this
     */
    public function consumeTokens(\SplStack $operandStack)
    {
        $this->rightOperand = $operandStack->pop();
        $this->leftOperand = $operandStack->pop();

        return $this;
    }
}
