<?php


namespace CondParse;


interface LexerInterface
{
    const TOKEN_NOT = '!';
    const TOKEN_AND = '&&';
    const TOKEN_OR = '||';
    const TOKEN_BRACKET_OPEN = '(';
    const TOKEN_BRACKET_CLOSE = ')';
    const TOKEN_TRUE = 'true';
    const TOKEN_FALSE = 'false';

    const DEFAULT_TOKENS = [
        self::TOKEN_NOT,
        self::TOKEN_AND,
        self::TOKEN_OR,
        self::TOKEN_BRACKET_OPEN,
        self::TOKEN_BRACKET_CLOSE,
        self::TOKEN_TRUE,
        self::TOKEN_FALSE
    ];

    /**
     * @param string $conditionString
     * @param TokenMap $tokenMap
     * @return \Traversable <LexerToken>
     */
    function getTokenStream($conditionString, TokenMap $tokenMap);
}
