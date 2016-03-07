<?php


namespace CondParse;


use Prophecy\Argument;

/**
 * @covers CondParse\CondParse<extended>
 */
class CondParseTest extends \PHPUnit_Framework_TestCase
{
    public function testParseConditionString_callsMinions()
    {
        $conditionString = 'test';
        $testResult = 'result';

        $lexer = $this->prophesize(LexerInterface::class);
        $parser = $this->prophesize(ParserInterface::class);
        $tokenMap = $this->prophesize(TokenMap::class);

        $stream = new \ArrayObject;

        $lexer
            ->getTokenStream(Argument::is($conditionString), Argument::is($tokenMap->reveal()))
            ->shouldBeCalled()
            ->willReturn($stream);

        $parser
            ->parseTokenStream(Argument::is($stream), Argument::is($tokenMap->reveal()))
            ->shouldBeCalled()
            ->willReturn($testResult);


        $condParse = new CondParse($lexer->reveal(), $parser->reveal(), $tokenMap->reveal());
        $actualResult = $condParse->parseConditionString($conditionString);


        $this->assertEquals($testResult, $actualResult);
    }
}
