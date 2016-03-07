<?php


namespace CondParse;


class OperandStack
{
    /** @var \SplStack */
    private $stack;

    public function __construct(\SplStack $storage = null)
    {
        $this->stack = $storage ?: new \SplStack();
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
     * @return OperandInterface|null
     */
    public function top()
    {
        return $this->isEmpty()
            ? null
            : $this->stack->top();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->stack->isEmpty();
    }
}
