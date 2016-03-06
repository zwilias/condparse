<?php


namespace CondParse\ParseStrategy;


use CondParse\TokenMap;
use CondParse\TokenParserParameter;

class ToOperatorStack implements ParseStrategyInterface
{

    /**
     * @param TokenParserParameter $parserParameter
     * @return bool
     */
    public function shouldExecuteFor(TokenParserParameter $parserParameter)
    {
        return
            $parserParameter->getLexerToken()->isToken(TokenMap::TOKEN_BRACKET_OPEN)
            || $parserParameter->getOperatorStack()->isEmpty()
            || $parserParameter->getTokenMap()->compareOperatorPrecedence(
                $parserParameter->getLexerToken(),
                $parserParameter->getOperatorStack()->top()
            ) > 0;
    }

    /**
     * @param TokenParserParameter $parserParameter
     * @return void
     */
    public function executeFor(TokenParserParameter $parserParameter)
    {
        $parserParameter->getOperatorStack()->push($parserParameter->getLexerToken());
    }
}
