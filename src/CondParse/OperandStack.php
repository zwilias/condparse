<?php


namespace CondParse;


class OperandStack
{
    /** @var \SplStack */
    private $stack;

    public function __construct()
    {
        $this->stack = new \SplStack();
    }

    /**
     * @param OperandInterface $operand
     */
    public function push(OperandInterface $operand)
    {
        $this->stack->push($operand);
    }

    /**
     * @return OperandInterface
     */
    public function pop()
    {
        return $this->stack->pop();
    }

    /**
     * @return OperandInterface
     */
    public function top()
    {
        return $this->stack->top();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->stack->isEmpty();
    }
}
