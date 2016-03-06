<?php


namespace CondParse\ParseStrategy;


use CondParse\TokenParserParameter;

class Operand implements ParseStrategyInterface
{

    /**
     * @param TokenParserParameter $parserParameter
     * @return bool
     */
    public function shouldExecuteFor(TokenParserParameter $parserParameter)
    {
        return $parserParameter->getTokenMap()->isOperand($parserParameter->getLexerToken());
    }

    /**
     * @param TokenParserParameter $parserParameter
     * @return void
     */
    public function executeFor(TokenParserParameter $parserParameter)
    {
        $parserParameter->getOperandStack()->push(
            $parserParameter
                ->getTokenMap()
                ->buildOperand($parserParameter->getLexerToken())
                ->consumeTokens($parserParameter->getOperandStack())
        );
    }
}
