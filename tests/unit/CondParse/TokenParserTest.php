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
        $lexerToken = new LexerToken('token', 'value');

        $tokenMapProphecy->isOperand(Argument::is($lexerToken))->shouldBeCalled()->willReturn(true);
        $tokenMapProphecy->buildOperand(Argument::is($lexerToken))->shouldBeCalled()->willReturn($operandProphecy->reveal());
        $operandProphecy->consumeTokens(Argument::type(OperandStack::class))->shouldBeCalled()->willReturn($operandProphecy->reveal());

        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken($lexerToken, $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operandStack->top(), $this->equalTo($operandProphecy->reveal()));
    }

    public function testParseToken_openBrackets_pushesOperator()
    {
        $lexerToken = new LexerToken(TokenMap::TOKEN_BRACKET_OPEN, 'value');
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand($lexerToken)->shouldBeCalled()->willReturn(false);

        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken($lexerToken, $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->top(), $this->equalTo($lexerToken));
    }

    public function testParseToken_emptyOperatorStack_pushesOperator()
    {
        $lexerToken = new LexerToken('test', 'value');
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand($lexerToken)->shouldBeCalled()->willReturn(false);

        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken($lexerToken, $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->top(), $this->equalTo($lexerToken));
    }

    public function testParseToken_higherOperatorPrecedence_pushesOperator()
    {
        $testToken = new LexerToken('test', 'value');
        $otherToken = new LExerToken('other', 'value');
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);

        $tokenMapProphecy->isOperand(Argument::is($testToken))->shouldBeCalled()->willReturn(false);
        $tokenMapProphecy
            ->compareOperatorPrecedence(Argument::is($testToken), Argument::is($otherToken))
            ->shouldBeCalled()->willReturn(1);

        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();
        $operatorStack->push($otherToken);


        $this->parser->parseToken($testToken, $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->top(), $this->equalTo($testToken));
    }

    public function testParseToken_closingBracket_pushOperandsUntilOpeningBracketIsFound()
    {
        $openToken = new LexerToken(TokenMap::TOKEN_BRACKET_OPEN, '');
        $closeToken = new LexerToken(TokenMap::TOKEN_BRACKET_CLOSE, '');
        $randomToken = new LexerToken('random', 'token');

        $operandProphecy = $this->prophet->prophesize(OperandInterface::class);
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand(Argument::is($closeToken))->shouldBeCalled()->willReturn(false);
        $tokenMapProphecy
            ->buildOperand(Argument::is($randomToken))
            ->shouldBeCalled()->willReturn($operandProphecy->reveal());
        $tokenMapProphecy
            ->compareOperatorPrecedence(Argument::is($closeToken), Argument::is($randomToken))
            ->shouldBeCalled()->willReturn(-1);
        $operandProphecy
            ->consumeTokens(Argument::type(OperandStack::class))
            ->shouldBeCalled()->willReturn($operandProphecy->reveal());

        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();
        $operatorStack->push($openToken);
        $operatorStack->push($randomToken);


        $this->parser->parseToken($closeToken, $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operatorStack->isEmpty(), $this->isTrue());
    }

    public function testParseToken_skipWhiteSpace()
    {
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();


        $this->parser->parseToken(
            new LexerToken(TokenMap::TOKEN_WHITESPACE, 'whitespace'), $operandStack, $operatorStack, $tokenMapProphecy->reveal()
        );


        $this->assertThat($operandStack->isEmpty(), $this->isTrue());
        $this->assertThat($operatorStack->isEmpty(), $this->isTrue());
    }

    public function testParseToken_lowerPrecedenceOperator_pushOperandsUntilHigherPrecedenceOrEmpty()
    {
        $testToken = new LexerToken('test', '');
        $randomToken = new LexerToken('random', 'token');

        $operandProphecy = $this->prophet->prophesize(OperandInterface::class);
        $tokenMapProphecy = $this->prophet->prophesize(TokenMap::class);
        $tokenMapProphecy->isOperand(Argument::is($testToken))->shouldBeCalled()->willReturn(false);
        $tokenMapProphecy
            ->buildOperand(Argument::is($randomToken))
            ->shouldBeCalled()->willReturn($operandProphecy->reveal());
        $tokenMapProphecy
            ->compareOperatorPrecedence(Argument::is($testToken), Argument::is($randomToken))
            ->shouldBeCalled()->willReturn(-1);
        $operandProphecy
            ->consumeTokens(Argument::type(OperandStack::class))
            ->shouldBeCalled()->willReturn($operandProphecy->reveal());

        $operandStack = new OperandStack();
        $operatorStack = new \SplStack();
        $operatorStack->push($randomToken);


        $this->parser->parseToken($testToken, $operandStack, $operatorStack, $tokenMapProphecy->reveal());


        $this->assertThat($operandStack->isEmpty(), $this->isFalse());
        $this->assertThat($operatorStack->isEmpty(), $this->isFalse());
    }
}
