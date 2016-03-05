<?php


namespace CondParse;


class CondParse
{
    /** @var LexerInterface */
    private $lexer;
    /** @var ParserInterface */
    private $parser;
    /** @var TokenMap */
    private $tokenMap;

    /**
     * @param LexerInterface $lexer
     * @param ParserInterface $parser
     * @param TokenMap $tokenMap
     */
    public function __construct(LexerInterface $lexer, ParserInterface $parser, TokenMap $tokenMap)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        $this->tokenMap = $tokenMap;
    }

    /**
     * @param string $conditionString
     * @return OperandInterface
     */
    public function parseConditionString($conditionString)
    {
        return $this->parser->parseTokenStream(
            $this->lexer->getTokenStream($conditionString, $this->tokenMap),
            $this->tokenMap
        );
    }
}
