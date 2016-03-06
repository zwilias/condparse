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

        foreach ($tokenStream as $lexerToken) {
            $this->tokenParser->parseToken(
                new TokenParserParameter($lexerToken, $operandStack, $operatorStack, $tokenMap)
            );
        }

        $this->unfoldStack($tokenMap, $operatorStack, $operandStack);

        return $operandStack->top();
    }

    /**
     * @param TokenMap $tokenMap
     * @param \SplStack $operatorStack
     * @param OperandStack $operandStack
     */
    protected function unfoldStack(TokenMap $tokenMap, $operatorStack, $operandStack)
    {
        while (!$operatorStack->isEmpty()) {
            $operandStack->push(
                $tokenMap
                    ->buildOperand($operatorStack->pop())
                    ->consumeTokens($operandStack)
            );
        }
    }
}
