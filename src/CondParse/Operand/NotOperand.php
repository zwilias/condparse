<?php


namespace CondParse\Operand;


use CondParse\OperandInterface;

class NotOperand extends AbstractOperand
{
    /** @var OperandInterface */
    private $containedOperand;

    /** @return bool */
    function execute()
    {
        return ! $this->containedOperand->execute();
    }

    /**
     * @param \SplStack $operandStack <OperandInterface>
     * @return $this
     */
    function consumeTokens(\SplStack $operandStack)
    {
        $this->containedOperand = $operandStack->pop();
        return $this;
    }

    public function __toString()
    {
        return sprintf('NOT(%s)', $this->containedOperand);
    }
}
