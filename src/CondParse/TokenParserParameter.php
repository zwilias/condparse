<?php


namespace CondParse;


class TokenParserParameter
{
    /** @var LexerToken */
    private $lexerToken;
    /** @var OperandStack */
    private $operandStack;
    /** @var \SplStack */
    private $operatorStack;
    /** @var TokenMap  */
    private $tokenMap;

    /**
     * @param LexerToken $lexerToken
     * @param OperandStack $operandStack
     * @param \SplStack $operatorStack
     * @param TokenMap $tokenMap
     */
    public function __construct(
        LexerToken $lexerToken, OperandStack $operandStack, \SplStack $operatorStack, TokenMap $tokenMap
    ) {

        $this->lexerToken = $lexerToken;
        $this->operandStack = $operandStack;
        $this->operatorStack = $operatorStack;
        $this->tokenMap = $tokenMap;
    }

    /**
     * @return LexerToken
     */
    public function getLexerToken()
    {
        return $this->lexerToken;
    }

    /**
     * @return OperandStack
     */
    public function getOperandStack()
    {
        return $this->operandStack;
    }

    /**
     * @return \SplStack
     */
    public function getOperatorStack()
    {
        return $this->operatorStack;
    }

    /**
     * @return TokenMap
     */
    public function getTokenMap()
    {
        return $this->tokenMap;
    }
}
