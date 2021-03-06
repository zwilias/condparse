<?php


namespace CondParse\Operand;


use CondParse\LexerToken;
use CondParse\TokenMap;

/**
 * @covers CondParse\Operand\OrOperator<extended>
 */
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
        return new OrOperator(new LexerToken(TokenMap::TOKEN_OR, '||'));
    }

    public function getStringTemplate()
    {
        return 'OR(%s, %s)';
    }
}
