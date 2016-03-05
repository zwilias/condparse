<?php


namespace CondParse;


use Prophecy\Argument;
use Prophecy\Prophet;

class TokenParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var Prophet */
    private $prophet;
    /** @var Parser */
    private $parser;

    public function setUp()
    {
        $this->prophet = new Prophet;
        $this->parser = new TokenParser;
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    public function testParseToken_operandType_buildsOperand()
    {
        $operandProphecy = $this->prophet->prophesize(OperandInterface::class);
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);

        $tokenMapProphecy->isOperand(Argument::is('token'))->shouldBeCalled()->willReturn(true);
        $tokenMapProphecy->buildOperand(Argument::is('token'), Argument::is('value'))->shouldBeCalled()->willReturn($operandProphecy->reveal());
        $operandProphecy->consumeTokens(Argument::type(\SplStack::class))->shouldBeCalled()->willReturn($operandProphecy->reveal());

        $operandStack = new \SplStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken('token', 'value', $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operandStack->top(), $this->equalTo($operandProphecy->reveal()));
    }

    public function testParseToken_openBrackets_pushesOperator()
    {
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand(Argument::is(TokenMap::TOKEN_BRACKET_OPEN))->shouldBeCalled()->willReturn(false);

        $operandStack = new \SplStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken(TokenMap::TOKEN_BRACKET_OPEN, 'value', $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->top(), $this->equalTo([TokenMap::TOKEN_BRACKET_OPEN, 'value']));
    }

    public function testParseToken_emptyOperatorStack_pushesOperator()
    {
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand(Argument::is('test'))->shouldBeCalled()->willReturn(false);

        $operandStack = new \SplStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken('test', 'value', $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->top(), $this->equalTo(['test', 'value']));
    }

    public function testParseToken_higherOperatorPrecedence_pushesOperator()
    {
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);

        $tokenMapProphecy->isOperand(Argument::is('test'))->shouldBeCalled()->willReturn(false);
        $tokenMapProphecy
            ->compareOperatorPrecedence(Argument::is('test'), Argument::is('other'))
            ->shouldBeCalled()->willReturn(1);

        $operandStack = new \SplStack();
        $operatorStack = new \SplStack();
        $operatorStack->push(['other', 'value']);


        $this->parser->parseToken('test', 'value', $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->top(), $this->equalTo(['test', 'value']));
    }

    public function testParseToken_closingBracket_pushOperandsUntilOpeningBracketIsFound()
    {
        $operandProphecy = $this->prophet->prophesize(OperandInterface::class);
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand(Argument::is(TokenMap::TOKEN_BRACKET_CLOSE))->shouldBeCalled()->willReturn(false);
        $tokenMapProphecy
            ->buildOperand(Argument::is('random'), Argument::is('token'))
            ->shouldBeCalled()->willReturn($operandProphecy->reveal());
        $tokenMapProphecy
            ->compareOperatorPrecedence(Argument::is(TokenMap::TOKEN_BRACKET_CLOSE), Argument::is('random'))
            ->shouldBeCalled()->willReturn(-1);
        $operandProphecy->consumeTokens(Argument::type(\SplStack::class))->shouldBeCalled()->willReturn($operandProphecy->reveal());

        $operandStack = new \SplStack();
        $operatorStack = new \SplStack();
        $operatorStack->push([TokenMap::TOKEN_BRACKET_OPEN, 'value']);
        $operatorStack->push(['random', 'token']);


        $this->parser->parseToken(TokenMap::TOKEN_BRACKET_CLOSE, 'value', $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->isEmpty(), $this->isTrue());
    }

    public function testParseToken_skipWhiteSpace()
    {
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $operandStack = new \SplStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken(
            TokenMap::TOKEN_WHITESPACE, 'whitespace', $operandStack, $operatorStack, $tokenMapProphecy->reveal()
        );


        $this->assertThat($operandStack->isEmpty(), $this->isTrue());
        $this->assertThat($operatorStack->isEmpty(), $this->isTrue());
    }

    public function testParseToken_lowerPrecedenceOperator_pushOperandsUntilHigherPrecedenceOrEmpty()
    {
        $operandProphecy = $this->prophet->prophesize(OperandInterface::class);
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand(Argument::is('test'))->shouldBeCalled()->willReturn(false);
        $tokenMapProphecy
            ->buildOperand(Argument::is('random'), Argument::is('token'))
            ->shouldBeCalled()->willReturn($operandProphecy->reveal());
        $tokenMapProphecy
            ->compareOperatorPrecedence(Argument::is('test'), Argument::is('random'))
            ->shouldBeCalled()->willReturn(-1);
        $operandProphecy->consumeTokens(Argument::type(\SplStack::class))->shouldBeCalled()->willReturn($operandProphecy->reveal());

        $operandStack = new \SplStack();
        $operatorStack = new \SplStack();
        $operatorStack->push(['random', 'token']);


        $this->parser->parseToken('test', 'value', $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operandStack->isEmpty(), $this->isFalse());
        $this->assertThat($operatorStack->isEmpty(), $this->isFalse());
    }
}
