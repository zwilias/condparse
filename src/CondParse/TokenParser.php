<?php


namespace CondParse;


use CondParse\ParseStrategy\ClosingBracket;
use CondParse\ParseStrategy\DefaultStrategy;
use CondParse\ParseStrategy\Operand;
use CondParse\ParseStrategy\ParseStrategyInterface;
use CondParse\ParseStrategy\ToOperatorStack;
use CondParse\ParseStrategy\WhiteSpace;

class TokenParser
{
    const STRATEGIES = [
        WhiteSpace::class,
        Operand::class,
        ToOperatorStack::class,
        ClosingBracket::class,
        DefaultStrategy::class
    ];

    /** @var ParseStrategyInterface[] */
    private $strategies;

    public function __construct()
    {
        $this->strategies = array_map(function ($class) {
            return new $class();
        }, self::STRATEGIES);
    }

    /**
     * @param TokenParserParameter $parameter
     */
    public function parseToken(TokenParserParameter $parameter)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->shouldExecuteFor($parameter)) {
                $strategy->executeFor($parameter);
                return;
            }
        }
    }
}
