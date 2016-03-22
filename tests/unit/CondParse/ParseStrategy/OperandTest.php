<?php


namespace CondParse\ParseStrategy;


use CondParse\LexerToken;
use CondParse\OperandInterface;
use CondParse\OperandStack;
use CondParse\TokenMap;
use CondParse\TokenParserParameter;
use Prophecy\Argument;

class OperandTest extends \PHPUnit_Framework_TestCase
{
    /** @var Operand */
    private $operandStrategy;

    public function setUp()
    {
        $this->operandStrategy = new Operand;
    }

    public function testShouldExecuteFor_trueIfOperand()
    {
        $lexerToken = $this->prophesize(LexerToken::class);
        $tokenMap = $this->prophesize(TokenMap::class);
        $parserParam = $this->prophesize(TokenParserParameter::class);

        $tokenMap->isOperand(Argument::is($lexerToken->reveal()))->shouldBeCalled()->willReturn(true);
        $parserParam->getLexerToken()->willReturn($lexerToken->reveal());
        $parserParam->getTokenMap()->willReturn($tokenMap->reveal());


        $this->assertTrue($this->operandStrategy->shouldExecuteFor($parserParam->reveal()));
    }

    public function testShouldExecuteFor_falseIfNotOperand()
    {
        $lexerToken = $this->prophesize(LexerToken::class);
        $tokenMap = $this->prophesize(TokenMap::class);
        $parserParam = $this->prophesize(TokenParserParameter::class);

        $tokenMap->isOperand(Argument::is($lexerToken->reveal()))->shouldBeCalled()->willReturn(false);
        $parserParam->getLexerToken()->willReturn($lexerToken->reveal());
        $parserParam->getTokenMap()->willReturn($tokenMap->reveal());


        $this->assertFalse($this->operandStrategy->shouldExecuteFor($parserParam->reveal()));
    }

    public function testExecuteFor_pushesOperandToStack()
    {
        $lexerToken = $this->prophesize(LexerToken::class);
        $tokenMap = $this->prophesize(TokenMap::class);
        $operandStack = $this->prophesize(OperandStack::class);
        $operand = $this->prophesize(OperandInterface::class);

        $tokenMap
            ->buildOperand(Argument::is($lexerToken->reveal()))
            ->shouldBeCalled()
            ->willReturn($operand->reveal());

        $operand
            ->consumeTokens(Argument::is($operandStack->reveal()))
            ->shouldBeCalled()
            ->willReturn($operand->reveal());

        $operandStack
            ->push(Argument::is($operand->reveal()))
            ->shouldBeCalled();

        $parserParameter = $this->prophesize(TokenParserParameter::class);
        $parserParameter->getOperandStack()->willReturn($operandStack->reveal());
        $parserParameter->getLexerToken()->willReturn($lexerToken->reveal());
        $parserParameter->getTokenMap()->willReturn($tokenMap->reveal());


        $this->operandStrategy->executeFor($parserParameter->reveal());
    }
}
