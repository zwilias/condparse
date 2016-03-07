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
        ClosingBracket::class
    ];

    const FALLBACK_STRATEGY = DefaultStrategy::class;

    /** @var ParseStrategyInterface[] */
    private $strategies;
    /** @var ParseStrategyInterface */
    private $fallbackStrategy;

    public function __construct()
    {
        $this->strategies = array_map(function ($class) {
            return new $class();
        }, self::STRATEGIES);

        $fallbackClass = self::FALLBACK_STRATEGY;
        $this->fallbackStrategy = new $fallbackClass();
    }

    /**
     * @param TokenParserParameter $parameter
     */
    public function parseToken(TokenParserParameter $parameter)
    {
        foreach ($this->strategies as $strategy) {
            if (! $strategy->shouldExecuteFor($parameter)) {
                continue;
            }

            $strategy->executeFor($parameter);
            return;
        }

        $this->fallbackStrategy->executeFor($parameter);
    }
}
