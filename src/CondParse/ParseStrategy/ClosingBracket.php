<?php


namespace CondParse\ParseStrategy;


use CondParse\TokenMap;
use CondParse\TokenParserParameter;

class ClosingBracket implements ParseStrategyInterface
{

    /**
     * @param TokenParserParameter $parserParameter
     * @return bool
     */
    public function shouldExecuteFor(TokenParserParameter $parserParameter)
    {
        return $parserParameter->getLexerToken()->isToken(TokenMap::TOKEN_BRACKET_CLOSE);
    }

    /**
     * @param TokenParserParameter $parserParameter
     * @return void
     */
    public function executeFor(TokenParserParameter $parserParameter)
    {
        while (! $parserParameter->getOperatorStack()->top()->isToken(TokenMap::TOKEN_BRACKET_OPEN)) {
            $parserParameter->getOperandStack()->push(
                $parserParameter
                    ->getTokenMap()
                    ->buildOperand($parserParameter->getOperatorStack()->pop())
                    ->consumeTokens($parserParameter->getOperandStack())
            );
        }

        $parserParameter->getOperatorStack()->pop();
    }
}
