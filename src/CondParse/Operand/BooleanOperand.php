<?php


namespace CondParse\Operand;


use CondParse\TokenMap;

class BooleanOperand extends AbstractValueOperand
{
    /** @return bool */
    function execute()
    {
        return $this->token === TokenMap::TOKEN_TRUE;
    }

    public function __toString()
    {
        return (string)$this->token;
    }
}
