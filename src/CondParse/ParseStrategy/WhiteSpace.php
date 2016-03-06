<?php


namespace CondParse\ParseStrategy;


use CondParse\TokenMap;
use CondParse\TokenParserParameter;

class WhiteSpace implements ParseStrategyInterface
{

    /**
     * @param TokenParserParameter $parserParameter
     * @return bool
     */
    public function shouldExecuteFor(TokenParserParameter $parserParameter)
    {
        return $parserParameter->getLexerToken()->isToken(TokenMap::TOKEN_WHITESPACE);
    }

    /**
     * @param TokenParserParameter $parserParameter
     * @return void
     */
    public function executeFor(TokenParserParameter $parserParameter)
    {
        // Skip whitespace
        return;
    }
}
