<?php


namespace CondParse;


class TokenParser
{
    /**
     * @param LexerToken $lexerToken
     * @param OperandStack $operandStack
     * @param \SplStack $operatorStack
     * @param TokenMap $tokenMap
     */
    public function parseToken(LexerToken $lexerToken, OperandStack $operandStack, \SplStack $operatorStack, TokenMap $tokenMap)
    {
        if ($lexerToken->isToken(TokenMap::TOKEN_WHITESPACE)) {
            // skip whitespace
        } elseif ($tokenMap->isOperand($lexerToken)) {
            $operandStack->push(
                $tokenMap->buildOperand($lexerToken)->consumeTokens($operandStack)
            );
        } elseif (
            $lexerToken->isToken(TokenMap::TOKEN_BRACKET_OPEN)
            || $operatorStack->isEmpty()
            || $tokenMap->compareOperatorPrecedence($lexerToken, $operatorStack->top()) > 0
        ) {
            $operatorStack->push($lexerToken);
        } elseif ($lexerToken->isToken(TokenMap::TOKEN_BRACKET_CLOSE)) {
            while (! $operatorStack->top()->isToken(TokenMap::TOKEN_BRACKET_OPEN)) {
                $this->pushOperand($operandStack, $operatorStack, $tokenMap);
            }

            $operatorStack->pop();
        } else {
            while (! $operatorStack->isEmpty() && $tokenMap->compareOperatorPrecedence($lexerToken, $operatorStack->top()) <= 0) {
                $this->pushOperand($operandStack, $operatorStack, $tokenMap);
            }

            $operatorStack->push($lexerToken);
        }
    }


    /**
     * @param OperandStack $operandStack
     * @param \SplStack $operatorStack
     * @param TokenMap $tokenMap
     */
    public function pushOperand(OperandStack $operandStack, \SplStack $operatorStack, TokenMap $tokenMap)
    {
        $operandStack->push(
            $tokenMap->buildOperand($operatorStack->pop())->consumeTokens($operandStack)
        );
    }
}
