<?php


namespace CondParse\ParseStrategy;


use CondParse\LexerToken;
use CondParse\TokenMap;
use CondParse\TokenParserParameter;
use Prophecy\Argument;

class ToOperatorStackTest extends \PHPUnit_Framework_TestCase
{
    /** @var ToOperatorStack */
    private $toOperatorStackStrategy;

    public function setUp()
    {
        $this->toOperatorStackStrategy = new ToOperatorStack;
    }

    /**
     * @param bool $isOpenBracket
     * @param bool $isOperatorStackEmpty
     * @param int $currentTokenPrecedence
     * @param bool $expectedResult
     * @dataProvider shouldExecuteMatrix
     */
    public function testShouldExecuteFor($isOpenBracket, $isOperatorStackEmpty, $currentTokenPrecedence, $expectedResult)
    {
        $lexerToken = $this->prophesize(LexerToken::class);
        $operatorStack = $this->prophesize(\SplStack::class);
        $tokenMap = $this->prophesize(TokenMap::class);

        $dummyToken = $this->prophesize(LexerToken::class);

        $lexerToken->isToken(Argument::is(TokenMap::TOKEN_BRACKET_OPEN))->willReturn($isOpenBracket);

        $operatorStack->isEmpty()->willReturn($isOperatorStackEmpty);
        $operatorStack->top()->willReturn($dummyToken->reveal());

        $tokenMap
            ->compareOperatorPrecedence(Argument::is($lexerToken->reveal()), Argument::is($dummyToken->reveal()))
            ->willReturn($currentTokenPrecedence);

        $parserParam = $this->prophesize(TokenParserParameter::class);
        $parserParam->getLexerToken()->willReturn($lexerToken->reveal());
        $parserParam->getOperatorStack()->willReturn($operatorStack->reveal());
        $parserParam->getTokenMap()->willReturn($tokenMap->reveal());


        $this->assertThat(
            $this->toOperatorStackStrategy->shouldExecuteFor($parserParam->reveal()),
            $this->equalTo($expectedResult)
        );
    }

    public function testExecuteFor_pushesLexerTokenToOperatorStack()
    {
        $operatorStack = $this->prophesize(\SplStack::class);
        $lexerToken = $this->prophesize(LexerToken::class);
        $parserParam = $this->prophesize(TokenParserParameter::class);

        $operatorStack->push(Argument::is($lexerToken->reveal()))->shouldBeCalled();
        $parserParam->getLexerToken()->willReturn($lexerToken->reveal());
        $parserParam->getOperatorStack()->willReturn($operatorStack->reveal());

        
        $this->toOperatorStackStrategy->executeFor($parserParam->reveal());
    }

    public function shouldExecuteMatrix()
    {
        return [
            [true, false, 0, true],
            [false, true, 0, true],
            [false, false, 1, true],
            [true, true, 0, true],
            [false, true, 1, true],
            [true, true, 1, true],
            [false, false, 0, false]
        ];
    }
}
