<?php


namespace CondParse;


class TokenParser
{
    /**
     * @param string $token
     * @param string $value
     * @param \SplStack $operandStack
     * @param \SplStack $operatorStack
     * @param TokenMap $tokenMap
     */
    public function parseToken($token, $value, \SplStack $operandStack, \SplStack $operatorStack, TokenMap $tokenMap)
    {
        if ($token === TokenMap::TOKEN_WHITESPACE) {
            return;
        }

        if ($tokenMap->isOperand($token)) {
            $operandStack->push(
                $tokenMap->buildOperand($token, $value)->consumeTokens($operandStack)
            );
            return;
        }

        if (
            $token === TokenMap::TOKEN_BRACKET_OPEN
            || $operatorStack->isEmpty()
            || $tokenMap->compareOperatorPrecedence($token, $operatorStack->top()[0]) > 0
        ) {
            $operatorStack->push([$token, $value]);
            return;
        }

        if ($token === TokenMap::TOKEN_BRACKET_CLOSE) {
            while ($operatorStack->top()[0] !== TokenMap::TOKEN_BRACKET_OPEN) {
                $this->pushOperand($operandStack, $operatorStack, $tokenMap);
            }

            $operatorStack->pop();
            return;
        }

        while (! $operatorStack->isEmpty() && $tokenMap->compareOperatorPrecedence($token, $operatorStack->top()[0]) <= 0) {
            $this->pushOperand($operandStack, $operatorStack, $tokenMap);
        }

        $operatorStack->push([$token, $value]);
        return;
    }


    /**
     * @param \SplStack $operandStack
     * @param \SplStack $operatorStack
     * @param TokenMap $tokenMap
     */
    public function pushOperand(\SplStack $operandStack, \SplStack $operatorStack, TokenMap $tokenMap)
    {
        list($operatorToken, $operatorValue) = $operatorStack->pop();

        $operandStack->push(
            $tokenMap->buildOperand($operatorToken, $operatorValue)->consumeTokens($operandStack)
        );
    }
}
