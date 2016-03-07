<?php


namespace CondParse\ParseStrategy;


use CondParse\LexerToken;
use CondParse\OperandInterface;
use CondParse\OperandStack;
use CondParse\TokenMap;
use CondParse\TokenParserParameter;
use Prophecy\Argument;

/**
 * @covers CondParse\ParseStrategy\ClosingBracket
 */
class ClosingBracketTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClosingBracket */
    private $closingBracket;

    public function setUp()
    {
        $this->closingBracket = new ClosingBracket;
    }

    public function testShouldExecuteFor_BracketClose_True()
    {
        $parseParameter = $this->prophesize(TokenParserParameter::class);
        $parseParameter->getLexerToken()->shouldBeCalled()->willReturn(
            new LexerToken(TokenMap::TOKEN_BRACKET_CLOSE, 'true')
        );


        $this->assertTrue($this->closingBracket->shouldExecuteFor($parseParameter->reveal()));
    }

    public function testShouldExecuteFor_OtherToken_False()
    {
        $parseParameter = $this->prophesize(TokenParserParameter::class);
        $parseParameter->getLexerToken()->shouldBeCalled()->willReturn(
            new LexerToken('random', 'true')
        );


        $this->assertFalse($this->closingBracket->shouldExecuteFor($parseParameter->reveal()));
    }

    public function testExecuteFor()
    {
        $operandStack = $this->prophesize(OperandStack::class);
        $operatorStack = $this->prophesize(\SplStack::class);
        $tokenMap = $this->prophesize(TokenMap::class);

        $operatorStack
            ->top()
            ->shouldBeCalled()
            ->will(function () use ($operatorStack) {
                $operatorStack
                    ->top()
                    ->willReturn(
                        new LexerToken(TokenMap::TOKEN_BRACKET_OPEN, '')
                    );

                return new LexerToken(TokenMap::TOKEN_BRACKET_CLOSE, '');
            });

        $operatorStack
            ->pop()
            ->shouldBeCalled()
            ->willReturn(new LexerToken(TokenMap::TOKEN_BRACKET_CLOSE, ''));

        $operandSpy = $this->prophesize(OperandInterface::class);
        $operandSpy->consumeTokens(Argument::is($operandStack->reveal()))->shouldBeCalled()->willReturn($operandSpy->reveal());
        $operandStack->push(Argument::is($operandSpy->reveal()))->shouldBeCalled();
        $tokenMap->buildOperand(Argument::type(LexerToken::class))->shouldBeCalled()->willReturn($operandSpy->reveal());

        $parseParameter = new TokenParserParameter(
            new LexerToken('bla', 'bla'),
            $operandStack->reveal(),
            $operatorStack->reveal(),
            $tokenMap->reveal()
        );


        $this->closingBracket->executeFor($parseParameter);
    }
}
