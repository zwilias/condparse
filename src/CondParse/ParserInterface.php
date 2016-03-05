<?php


namespace CondParse;


use CondParse\Exception\ParserException;

interface ParserInterface
{
    /**
     * @param \Traversable $tokenStream
     * @param TokenMap $tokenMap
     * @return OperandInterface
     * @throws ParserException
     */
    function parseTokenStream(\Traversable $tokenStream, TokenMap $tokenMap);
}
