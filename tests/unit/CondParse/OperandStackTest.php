<?php


namespace CondParse;


use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers CondParse\OperandStack<extended>
 */
class OperandStackTest extends \PHPUnit_Framework_TestCase
{
    /** @var OperandStack */
    private $operandStack;
    /** @var ObjectProphecy */
    private $storageProphesy;

    public function setUp()
    {
        $this->storageProphesy = $this->prophesize(\SplStack::class);
        $this->operandStack = new OperandStack($this->storageProphesy->reveal());
    }

    public function testPush_pushesOperandInterface()
    {
        $operand = $this->prophesize(OperandInterface::class);
        $this->storageProphesy->push(Argument::is($operand->reveal()))->shouldBeCalled();


        $this->operandStack->push($operand->reveal());
    }

    public function testPop_popsOperandInterface()
    {
        $operand = $this->prophesize(OperandInterface::class);
        $this->storageProphesy->pop()->shouldBeCalled()->willReturn($operand->reveal());


        $this->assertThat(
            $this->operandStack->pop(),
            $this->equalTo($operand->reveal())
        );
    }

    public function testIsEmpty_returnsEmptyOfStorage()
    {
        $this->storageProphesy->isEmpty()->shouldBeCalled()->willReturn(true);


        $this->assertTrue($this->operandStack->isEmpty());
    }

    public function testTop_returnsNullIfEmpty()
    {
        $this->storageProphesy->isEmpty()->shouldBeCalled()->willReturn(true);


        $this->assertNull(
            $this->operandStack->top()
        );
    }

    public function testTop_returnsTopIfNotEmpty()
    {
        $operand = $this->prophesize(OperandInterface::class);
        $this->storageProphesy->isEmpty()->shouldBeCalled()->willReturn(false);
        $this->storageProphesy->top()->shouldBeCalled()->willReturn($operand->reveal());


        $this->assertThat(
            $this->operandStack->top(),
            $this->equalTo($operand->reveal())
        );
    }
}
