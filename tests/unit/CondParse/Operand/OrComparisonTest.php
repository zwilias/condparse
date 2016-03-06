<?php


namespace CondParse\Operand;


use CondParse\TokenMap;

class OrComparisonTest extends AbstractComparisonTest
{
    public function andComparisonProvider()
    {
        return [
            [true, true, true],
            [true, false, true],
            [false, true, true],
            [false, false, false]
        ];
    }

    /** @return AbstractLeftRightOperator */
    public function getComparator()
    {
        return new OrOperator(TokenMap::TOKEN_OR, '||');
    }
}