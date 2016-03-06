<?php


namespace CondParse;


use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var Prophet */
    private $prophet;
    /** @var ObjectProphecy */
    private $tokenParserProphesy;
    /** @var ObjectProphecy */
    private $tokenMapProphesy;
    /** @var Parser */
    private $parser;

    public function setUp()
    {
        $this->prophet = new Prophet;

        $this->tokenParserProphesy = $this->prophet->prophesize(TokenParser::class);
        $this->tokenMapProphesy = $this->prophet->prophesize(TokenMap::class);

        $this->parser = new Parser($this->tokenParserProphesy->reveal());
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    public function testParseTokenStream_emptyStream_returnsNull()
    {
        $this->assertNull($this->parser->parseTokenStream(new \ArrayIterator, $this->tokenMapProphesy->reveal()));
    }

    public function testParseTokenStream_parseTokenCalledForEachToken()
    {
        $tokenStream = new \ArrayIterator([new LexerToken('test', 'token')]);
        $this->tokenParserProphesy
            ->parseToken(Argument::type(TokenParserParameter::class))
            ->shouldBeCalled();


        $this->assertNull($this->parser->parseTokenStream($tokenStream, $this->tokenMapProphesy->reveal()));
    }

    public function testParseTokenStream_whileOperatorsLeft_operandsArePushed()
    {
        $tokenStream = new \ArrayIterator([new LexerToken('test', 'token')]);
        $this->tokenParserProphesy
            ->parseToken(Argument::type(TokenParserParameter::class))
            ->shouldBeCalled();


        $this->assertNull($this->parser->parseTokenStream($tokenStream, $this->tokenMapProphesy->reveal()));
    }

    public function testParseTokenStream_returnsTopOfOperandStack_ifNotEmpty()
    {
        $operandSpyProphesy = $this->prophet->prophesize(OperandInterface::class);

        $tokenStream = new \ArrayIterator([new LexerToken('test', 'token')]);
        $this->tokenParserProphesy
            ->parseToken(Argument::type(TokenParserParameter::class))
            ->shouldBeCalled()
            ->will(function ($args) use ($operandSpyProphesy) {
                $args[0]->getOperandStack()->push($operandSpyProphesy->reveal());
            });


        $this->assertThat(
            $this->parser->parseTokenStream($tokenStream, $this->tokenMapProphesy->reveal()),
            $this->equalTo($operandSpyProphesy->reveal())
        );
    }
}
