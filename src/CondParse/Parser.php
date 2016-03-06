<?php


namespace CondParse;


use CondParse\Exception\ParserException;

class Parser implements ParserInterface
{
    /** @var TokenParser */
    private $tokenParser;

    public function __construct(TokenParser $tokenParser = null)
    {
        $this->tokenParser = $tokenParser ?: new TokenParser;
    }

    /**
     * @param \Traversable $tokenStream
     * @param TokenMap $tokenMap
     * @return OperandInterface
     * @throws ParserException
     */
    public function parseTokenStream(\Traversable $tokenStream, TokenMap $tokenMap)
    {
        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();

        foreach ($tokenStream as $tokenEntry) {
            list($token, $value) = $tokenEntry;

            $this->tokenParser->parseToken($token, $value, $operandStack, $operatorStack, $tokenMap);
        }

        while (! $operatorStack->isEmpty()) {
            $this->tokenParser->pushOperand($operandStack, $operatorStack, $tokenMap);
        }

        return $operandStack->isEmpty()
            ? null
            : $operandStack->top();
    }
}
