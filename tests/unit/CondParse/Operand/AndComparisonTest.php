<?php


namespace CondParse\Operand;


use CondParse\LexerToken;
use CondParse\TokenMap;

/**
 * @covers CondParse\Operand\AndOperator<extended>
 */
class AndComparisonTest extends AbstractComparisonTest
{
    public function andComparisonProvider()
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
            [false, false, false],
        ];
    }

    /** @return AbstractLeftRightOperator */
    public function getComparator()
    {
        return new AndOperator(new LexerToken(TokenMap::TOKEN_AND, '&&'));
    }

    public function getStringTemplate()
    {
        return 'AND(%s, %s)';
    }
}
