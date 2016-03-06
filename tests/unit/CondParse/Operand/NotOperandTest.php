<?php


namespace unit\CondParse\Operand;


use CondParse\LexerToken;
use CondParse\Operand\NotOperand;
use CondParse\OperandInterface;
use CondParse\OperandStack;
use CondParse\TokenMap;
use Prophecy\Prophet;

class NotOperandTest extends \PHPUnit_Framework_TestCase
{
    /** @var Prophet */
    private $prophet;

    public function setUp()
    {
        $this->prophet = new Prophet;
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    /**
     * @param bool $input
     * @param bool $output
     * @dataProvider notOperandProvider
     */
    public function testExecute($input, $output)
    {
        $containedOperandProphesy = $this->prophesize(OperandInterface::class);
        $containedOperandProphesy->execute()->shouldBeCalled()->willReturn($input);

        $operandStack = new OperandStack();
        $operandStack->push($containedOperandProphesy->reveal());


        $notOperand = new NotOperand(new LexerToken(TokenMap::TOKEN_NOT, 'whatever'));
        $notOperand->consumeTokens($operandStack);


        $this->assertThat($notOperand->execute(), $this->equalTo($output));
    }

    public function testToString()
    {
        $containedOperand = $this->prophesize(OperandInterface::class);
        $containedOperand->__toString()->willReturn('test');

        $operandStack = new OperandStack();
        $operandStack->push($containedOperand->reveal());

        $notOperand = new NotOperand(new LexerToken(TokenMap::TOKEN_NOT, 'whatever'));
        $notOperand->consumeTokens($operandStack);


        $this->assertThat((string) $notOperand, $this->equalTo('NOT(test)'));
    }

    public function notOperandProvider()
    {
        return [
            [true, false],
            [false, true]
        ];
    }
}
