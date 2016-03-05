<?php


namespace unit\CondParse\Operand;


use CondParse\Operand\NotOperand;
use CondParse\OperandInterface;
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

        $operandStack = new \SplStack();
        $operandStack->push($containedOperandProphesy->reveal());


        $notOperand = new NotOperand(TokenMap::TOKEN_NOT, 'whatever');
        $notOperand->consumeTokens($operandStack);


        $this->assertThat($notOperand->execute(), $this->equalTo($output));
    }

    public function notOperandProvider()
    {
        return [
            [true, false],
            [false, true]
        ];
    }
}
