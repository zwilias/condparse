<?php


namespace CondParse\Operand;


class AndOperator extends AbstractLeftRightOperator
{
    /** @return bool */
    function execute()
    {
        return $this->leftOperand->execute() && $this->rightOperand->execute();
    }

    public function __toString()
    {
        return sprintf('AND(%s, %s)', $this->leftOperand, $this->rightOperand);
    }
}
