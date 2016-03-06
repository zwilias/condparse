<?php


namespace CondParse\ParseStrategy;


use CondParse\TokenParserParameter;

class DefaultStrategy implements ParseStrategyInterface
{

    /**
     * @param TokenParserParameter $parserParameter
     * @return bool
     */
    public function shouldExecuteFor(TokenParserParameter $parserParameter)
    {
        return true;
    }

    /**
     * @param TokenParserParameter $parserParameter
     * @return void
     */
    public function executeFor(TokenParserParameter $parserParameter)
    {
        while (
            ! $parserParameter->getOperatorStack()->isEmpty()
            && $parserParameter->getTokenMap()
                ->compareOperatorPrecedence(
                    $parserParameter->getLexerToken(),
                    $parserParameter->getOperatorStack()->top()
                ) <= 0
        ) {
            $parserParameter->getOperandStack()->push(
                $parserParameter
                    ->getTokenMap()
                    ->buildOperand($parserParameter->getOperatorStack()->pop())
                    ->consumeTokens($parserParameter->getOperandStack())
            );
        }

        $parserParameter->getOperatorStack()->push($parserParameter->getLexerToken());
    }
}
