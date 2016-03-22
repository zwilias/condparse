<?php


namespace CondParse\ParseStrategy;


use CondParse\LexerToken;
use CondParse\TokenMap;
use CondParse\TokenParserParameter;
use Prophecy\Argument;

class WhiteSpaceTest extends \PHPUnit_Framework_TestCase
{
    /** @var WhiteSpace */
    private $whiteSpaceStrategy;

    public function setUp()
    {
        $this->whiteSpaceStrategy = new WhiteSpace;
    }

    public function testShouldExecuteFor_trueIfWhitespace()
    {
        $lexerToken = $this->prophesize(LexerToken::class);
        $parserParam = $this->prophesize(TokenParserParameter::class);

        $lexerToken->isToken(Argument::is(TokenMap::TOKEN_WHITESPACE))->shouldBeCalled()->willReturn(true);
        $parserParam->getLexerToken()->willReturn($lexerToken->reveal());


        $this->assertTrue($this->whiteSpaceStrategy->shouldExecuteFor($parserParam->reveal()));
    }

    public function testShouldExecuteFor_falseIfNotWhiteSpace()
    {
        $lexerToken = $this->prophesize(LexerToken::class);
        $parserParam = $this->prophesize(TokenParserParameter::class);

        $lexerToken->isToken(Argument::is(TokenMap::TOKEN_WHITESPACE))->shouldBeCalled()->willReturn(false);
        $parserParam->getLexerToken()->willReturn($lexerToken->reveal());


        $this->assertFalse($this->whiteSpaceStrategy->shouldExecuteFor($parserParam->reveal()));
    }

    public function testExecuteFor_DoesNotDoMuchOfAnythingWhatsoeverReally()
    {
        $parserParam = $this->prophesize(TokenParserParameter::class);


        $this->assertNull($this->whiteSpaceStrategy->executeFor($parserParam->reveal()));
    }
}
