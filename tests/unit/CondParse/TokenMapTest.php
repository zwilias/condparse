<?php


namespace CondParse;


use CondParse\Exception\TokenMapException;
use CondParse\Operand\AndOperator;
use CondParse\Operand\BooleanOperand;

/**
 * @covers CondParse\TokenMap<extended>
 */
class TokenMapTest extends \PHPUnit_Framework_TestCase
{
    /** @var TokenMap */
    private $tokenMap;

    public function setUp()
    {
        $this->tokenMap = new TokenMap;
    }

    public function testRegisterOperand()
    {
        $this->tokenMap->registerOperand('maybe', 'maybe', BooleanOperand::class);


        $this->assertThat(
            $this->tokenMap->getTokens(),
            $this->logicalAnd(
                $this->arrayHasKey('maybe'),
                $this->contains('maybe')
            )
        );

        $this->assertTrue(
            $this->tokenMap->isOperand(new LexerToken('maybe', 'maybe'))
        );
    }

    public function testRegisterOperator()
    {
        $this->tokenMap->registerOperator('maybe', 'maybe', 0, BooleanOperand::class);


        $this->assertThat(
            $this->tokenMap->getTokens(),
            $this->logicalAnd(
                $this->arrayHasKey('maybe'),
                $this->contains('maybe')
            )
        );

        $this->assertFalse(
            $this->tokenMap->isOperand(new LexerToken('maybe', 'maybe'))
        );
    }

    public function testCompareOperatorPrecedence()
    {
        $this->tokenMap->registerOperator(
            'maybe',
            'maybe',
            TokenMap::DEFAULT_OPERATOR_PRECEDENCE[TokenMap::TOKEN_AND] - 1,
            BooleanOperand::class
        );

        $this->assertThat(
            $this->tokenMap->compareOperatorPrecedence(
                new LexerToken('maybe', 'value'),
                new LexerToken(TokenMap::TOKEN_AND, 'value')
            ),
            $this->lessThan(0)
        );
    }

    public function testBuildOperand_buildsOperand()
    {
        $operand = $this->tokenMap->buildOperand(new LexerToken(TokenMap::TOKEN_TRUE, 'true'));


        $this->assertThat(
            $operand,
            $this->isInstanceOf(BooleanOperand::class)
        );
    }

    /**
     * @param string $name
     * @param string $regex
     * @param string $class
     * @dataProvider provideInvalidOperandInput
     */
    public function testRegisterOperand_invalidInput_fails($name, $regex, $class)
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
    public function testRegisterOperator_invalidInput_fails($name, $regex, $precedence, $class)
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
