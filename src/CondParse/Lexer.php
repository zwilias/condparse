<?php


namespace CondParse;


use CondParse\Exception\LexerException;

class Lexer implements LexerInterface
{
    /** @var callable[] */
    private $postFunctions = [];

    /**
     * @param callable $postFunction
     * @return $this
     */
    public function registerPostFunction(callable $postFunction)
    {
        $this->postFunctions[] = $postFunction;
        return $this;
    }

    /**
     * @param TokenMap $tokenMap
     * @return string
     */
    protected function buildRegex(TokenMap $tokenMap)
    {
        return sprintf(
            '/%s/Si',
            join('|',
                array_map(function ($key, $value) {
                    return sprintf('(?P<%s>%s)', $key, $value);
                }, array_keys($tokenMap->getTokens()), $tokenMap->getTokens())
            )
        );
    }

    /**
     * @param string $token
     * @param string $match
     * @return string
     */
    protected function applyPostFunctions($token, $match)
    {
        foreach ($this->postFunctions as $postFunction) {
            $match = call_user_func($postFunction, $token, $match);
        }

        return $match;
    }

    /**
     * @param string $conditionString
     * @param string $regex
     * @return \Traversable <string>
     * @throws LexerException
     */
    protected function getTokenStreamWithRegex($conditionString, $regex)
    {
        $offSet = 0;

        $matches = [];
        while (preg_match($regex, $conditionString, $matches, 0, $offSet) !== 0) {
            list($match, $token) = $this->extractMatch($matches);

            $this->checkMatchOffset($conditionString, $match, $offSet);

            $offSet += strlen($match);

            yield [$token, $this->applyPostFunctions($token, $match)];
        }
    }

    /**
     * @param string $conditionString
     * @param TokenMap $tokenMap
     * @return \Traversable <string>
     */
    public function getTokenStream($conditionString, TokenMap $tokenMap) {
        return $this->getTokenStreamWithRegex($conditionString, $this->buildRegex($tokenMap));
    }

    /**
     * @param $matches
     * @return array
     */
    protected function extractMatch($matches)
    {
        $matches = array_filter($matches, function ($value, $key) {
            return is_string($key) && !empty($value);
        }, ARRAY_FILTER_USE_BOTH);

        $match = each($matches);
        $token = $match['key'];
        $match = $match['value'];
        return [$match, $token];
    }

    /**
     * @param $conditionString
     * @param $match
     * @param $offSet
     * @throws LexerException
     */
    protected function checkMatchOffset($conditionString, $match, $offSet)
    {
        if (($matchOffset = strpos($conditionString, $match, $offSet)) !== $offSet) {
            throw new LexerException(sprintf(
                'Found unrecognized token <%s> at offset %d',
                substr($conditionString, $offSet, ($matchOffset - $offSet)),
                $offSet
            ));
        }
    }
}