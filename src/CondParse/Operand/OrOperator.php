<?php


namespace CondParse\Operand;


class OrOperator extends AbstractLeftRightOperator
{
    /** @return bool */
    function execute()
    {
        return $this->leftOperand->execute() || $this->rightOperand->execute();
    }

    public function __toString()
    {
        return sprintf('OR(%s, %s)', $this->leftOperand, $this->rightOperand);
    }
}
