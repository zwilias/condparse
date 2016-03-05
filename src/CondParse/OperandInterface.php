<?php


namespace CondParse;


interface OperandInterface
{
    /** @return mixed */
    function execute();

    /**
     * @param \SplStack $operandStack <OperandInterface>
     * @return $this
     */
    function consumeTokens(\SplStack $operandStack);

    /** @return string */
    function __toString();
}
