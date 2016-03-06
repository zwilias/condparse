<?php


namespace CondParse\Operand;


use CondParse\OperandInterface;
use CondParse\OperandStack;

class NotOperand extends AbstractOperand
{
    /** @var OperandInterface */
    private $containedOperand;

    /** @return bool */
    public function execute()
    {
        return ! $this->containedOperand->execute();
    }

    /**
     * @param OperandStack $operandStack
     * @return $this
     */
    public function consumeTokens(OperandStack $operandStack)
    {
        $this->containedOperand = $operandStack->pop();
        return $this;
    }

    public function __toString()
    {
        return sprintf('NOT(%s)', $this->containedOperand);
    }
}
