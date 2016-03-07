<?php


namespace CondParse\Operand;


use CondParse\LexerToken;
use CondParse\OperandStack;
use CondParse\TokenMap;

/**
 * @covers CondParse\Operand\BooleanOperand<extended>
 */
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
        $operand = new BooleanOperand(new LexerToken($token, $value));


        $this->assertEquals($expectedResult, $operand->execute());
    }

    public function testToString()
    {
        $operand = new BooleanOperand(new LexerToken('test', 'bla'));


        $this->assertEquals('test', (string) $operand);
    }

    public function testConsumerTokens_returnsSelf()
    {
        $operand = new BooleanOperand(new LexerToken('test', 'it'));
        $operandStack = new OperandStack();


        $this->assertEquals($operand, $operand->consumeTokens($operandStack));
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
