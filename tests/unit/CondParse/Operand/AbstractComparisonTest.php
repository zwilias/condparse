<?php


namespace CondParse\Operand;


use CondParse\OperandInterface;
use CondParse\OperandStack;
use CondParse\TokenMap;
use Prophecy\Prophet;

abstract class AbstractComparisonTest extends \PHPUnit_Framework_TestCase
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
     * @param bool $leftVal
     * @param bool $rightVal
     * @param bool $expectedResult
     * @dataProvider andComparisonProvider
     */
    public function testExecute($leftVal, $rightVal, $expectedResult)
    {
        $leftValProphecy = $this->prophet->prophesize(OperandInterface::class);
        $rightValProphecy = $this->prophet->prophesize(OperandInterface::class);

        $leftValProphecy->execute()->shouldBeCalled()->willReturn($leftVal);
        $rightValProphecy->execute()->willReturn($rightVal);

        $operandStack = new OperandStack();
        $operandStack->push($leftValProphecy->reveal());
        $operandStack->push($rightValProphecy->reveal());


        $operand = $this->getComparator();
        $operand->consumeTokens($operandStack);


        $this->assertThat($operand->execute(), $this->equalTo($expectedResult));
    }

    /**
     * @param bool $leftVal
     * @param bool $rightVal
     * @dataProvider andComparisonProvider
     */
    public function testToString($leftVal, $rightVal)
    {
        $leftValValue = $leftVal ? TokenMap::TOKEN_TRUE : TokenMap::TOKEN_FALSE;
        $rightValValue = $rightVal ? TokenMap::TOKEN_TRUE : TokenMap::TOKEN_FALSE;
        $leftValProphecy = $this->prophet->prophesize(OperandInterface::class);
        $rightValProphecy = $this->prophet->prophesize(OperandInterface::class);

        $leftValProphecy->__toString()->shouldBeCalled()->willReturn($leftValValue);
        $rightValProphecy->__toString()->shouldBeCalled()->willReturn($rightValValue);

        $operandStack = new OperandStack();
        $operandStack->push($leftValProphecy->reveal());
        $operandStack->push($rightValProphecy->reveal());


        $operand = $this->getComparator();
        $operand->consumeTokens($operandStack);


        $this->assertThat((string) $operand, $this->equalTo(
            sprintf($this->getStringTemplate(), $leftValValue, $rightValValue)
        ));
    }

    /** @return AbstractLeftRightOperator */
    abstract public function getComparator();

    abstract public function andComparisonProvider();

    abstract public function getStringTemplate();
}
