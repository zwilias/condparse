<?php


namespace CondParse;


use CondParse\Exception\TokenMapException;
use CondParse\Operand\AndOperator;

class TokenMapTest extends \PHPUnit_Framework_TestCase
{
    /** @var TokenMap */
    private $tokenMap;

    public function setUp()
    {
        $this->tokenMap = new TokenMap;
    }

    /**
     * @param string $name
     * @param string $regex
     * @param string $class
     * @dataProvider provideInvalidOperandInput
     */
    public function testRegisterOperand($name, $regex, $class)
    {
        $this->expectException(TokenMapException::class);
        $this->tokenMap->registerOperand($name, $regex, $class);
    }

    /**
     * @param string $name
     * @param string $regex
     * @param int $precedence
     * @param string $class
     * @dataProvider provideInvalidOperatorInput
     */
    public function testRegisterOperator($name, $regex, $precedence, $class)
    {
        $this->expectException(TokenMapException::class);
        $this->tokenMap->registerOperator($name, $regex, $precedence, $class);
    }

    public function provideInvalidOperandInput()
    {
        return array_map(function ($entry) {
            return [$entry[0], $entry[1], $entry[2]];
        }, $this->provideInvalidOperatorInput());
    }

    public function provideInvalidOperatorInput()
    {
        return [
            'notAString' => [false, '', 0, AndOperator::class],
            'alreadyDefined' => [TokenMap::TOKEN_AND, '', 0, AndOperator::class],
            'notAndOperand' => ['test', 'test', 0, TokenMapTest::class]
        ];
    }
}
