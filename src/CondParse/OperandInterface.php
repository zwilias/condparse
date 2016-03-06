<?php


namespace CondParse;


interface OperandInterface
{
    /** @return mixed */
    function execute();

    /**
     * @param OperandStack $operandStack
     * @return $this
     */
    function consumeTokens(OperandStack $operandStack);

    /** @return string */
    function __toString();
}
