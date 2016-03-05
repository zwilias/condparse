<?php


namespace CondParse\Operand;


use CondParse\TokenMap;

class BooleanOperandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $token
     * @param string $value
     * @param bool $expectedResult
     * @dataProvider booleanOperandProvider
     */
    public function testExecute($token, $value, $expectedResult)
    {
        $operand = new BooleanOperand($token, $value);


        $this->assertEquals($expectedResult, $operand->execute());
    }

    public function booleanOperandProvider()
    {
        return [
            [TokenMap::TOKEN_TRUE, 'true', true],
            [TokenMap::TOKEN_FALSE, 'false', false],
            [TokenMap::TOKEN_TRUE, '123', true],
            [TokenMap::TOKEN_FALSE, '321', false]
        ];
    }
}
