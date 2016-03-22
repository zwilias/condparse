<?php


namespace CondParse\ParseStrategy;


use CondParse\LexerToken;
use CondParse\OperandInterface;
use CondParse\OperandStack;
use CondParse\TokenMap;
use CondParse\TokenParserParameter;
use Prophecy\Argument;

class DefaultStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var DefaultStrategy */
    private $defaultStrategy;

    public function setUp()
    {
        $this->defaultStrategy = new DefaultStrategy;
    }

    public function testShouldExecuteFor_shouldReturnTrue()
    {
        $parseParam = $this->prophesize(TokenParserParameter::class);


        $this->assertTrue($this->defaultStrategy->shouldExecuteFor($parseParam->reveal()));
    }

    public function testExecuteFor_EmptyOperatorStack_PushesLexerToken()
    {
        $operatorStack = $this->prophesize(\SplStack::class);
        $lexerToken = $this->prophesize(LexerToken::class);

        $operatorStack->isEmpty()->shouldBeCalled()->willReturn(true);
        $operatorStack->push(Argument::is($lexerToken->reveal()))->shouldBeCalled();

        $parserParameter = $this->prophesize(TokenParserParameter::class);
        $parserParameter->getOperatorStack()->willReturn($operatorStack->reveal());
        $parserParameter->getLexerToken()->willReturn($lexerToken->reveal());


        $this->defaultStrategy->executeFor($parserParameter->reveal());
    }

    public function testExecuteFor_NonEmptyOperatorStack_HigherPrecedence_PushesLexerToken()
    {
        $operatorStack = $this->prophesize(\SplStack::class);
        $lexerToken = $this->prophesize(LexerToken::class);
        $otherLexerToken = $this->prophesize(LexerToken::class);
        $tokenMap = $this->prophesize(TokenMap::class);

        $operatorStack->isEmpty()->shouldBeCalled()->willReturn(false);

        $operatorStack->top()->shouldBeCalled()->willReturn($otherLexerToken->reveal());
        $operatorStack->push(Argument::is($lexerToken->reveal()))->shouldBeCalled();

        $tokenMap
            ->compareOperatorPrecedence(Argument::is($lexerToken->reveal()), Argument::is($otherLexerToken->reveal()))
            ->shouldBeCalled()
            ->willReturn(1);

        $parserParameter = $this->prophesize(TokenParserParameter::class);
        $parserParameter->getOperatorStack()->willReturn($operatorStack->reveal());
        $parserParameter->getLexerToken()->willReturn($lexerToken->reveal());
        $parserParameter->getTokenMap()->willReturn($tokenMap->reveal());


        $this->defaultStrategy->executeFor($parserParameter->reveal());
    }

    public function testExecuteFor_NonEmptyOperatorStack_LowerPrecedence_PushesOperand_PushesLexerToken()
    {
        $operatorStack = $this->prophesize(\SplStack::class);
        $lexerToken = $this->prophesize(LexerToken::class);
        $otherLexerToken = $this->prophesize(LexerToken::class);
        $tokenMap = $this->prophesize(TokenMap::class);
        $operand = $this->prophesize(OperandInterface::class);
        $operandStack = $this->prophesize(OperandStack::class);

        $operatorStack->isEmpty()->shouldBeCalled()->will(function () use ($operatorStack) {
            $operatorStack->isEmpty()->willReturn(true);

            return false;
        });

        $operatorStack->top()->shouldBeCalled()->willReturn($otherLexerToken->reveal());
        $operatorStack->pop()->shouldBeCalled()->willReturn($otherLexerToken->reveal());
        $operatorStack->push(Argument::is($lexerToken->reveal()))->shouldBeCalled();

        $tokenMap
            ->compareOperatorPrecedence(Argument::is($lexerToken->reveal()), Argument::is($otherLexerToken->reveal()))
            ->shouldBeCalled()
            ->willReturn(0);

        $tokenMap
            ->buildOperand(Argument::is($otherLexerToken->reveal()))
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
        $parserParameter->getOperatorStack()->willReturn($operatorStack->reveal());
        $parserParameter->getOperandStack()->willReturn($operandStack->reveal());
        $parserParameter->getLexerToken()->willReturn($lexerToken->reveal());
        $parserParameter->getTokenMap()->willReturn($tokenMap->reveal());


        $this->defaultStrategy->executeFor($parserParameter->reveal());
    }
}
