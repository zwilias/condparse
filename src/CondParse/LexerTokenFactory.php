<?php


namespace CondParse;


class LexerTokenFactory
{
    /**
     * @param string $token
     * @param mixed $value
     * @return LexerToken
     */
    public function buildLexerToken($token, $value)
    {
        // TODO: some flyweight like pattern?
        return new LexerToken($token, $value);
    }
}
