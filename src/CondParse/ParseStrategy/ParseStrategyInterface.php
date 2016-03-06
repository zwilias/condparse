<?php


namespace CondParse\ParseStrategy;


use CondParse\TokenParserParameter;

interface ParseStrategyInterface
{
    /**
     * @param TokenParserParameter $parserParameter
     * @return bool
     */
    public function shouldExecuteFor(TokenParserParameter $parserParameter);

    /**
     * @param TokenParserParameter $parserParameter
     * @return void
     */
    public function executeFor(TokenParserParameter $parserParameter);
}
