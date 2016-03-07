<?php


namespace CondParse;


class TokenParserParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct_exposesParameters()
    {
        $lexerToken = $this->prophesize(LexerToken::class);
        $operandStack = $this->prophesize(OperandStack::class);
        $operatorStack = $this->prophesize(\SplStack::class);
        $tokenMap = $this->prophesize(TokenMap::class);

        $tokenParserParameter = new TokenParserParameter(
            $lexerToken->reveal(),
            $operandStack->reveal(),
            $operatorStack->reveal(),
            $tokenMap->reveal()
        );


        $this->assertThat($tokenParserParameter->getOperandStack(),     $this->equalTo($operandStack->reveal()));
        $this->assertThat($tokenParserParameter->getOperatorStack(),    $this->equalTo($operatorStack->reveal()));
        $this->assertThat($tokenParserParameter->getLexerToken(),       $this->equalTo($lexerToken->reveal()));
        $this->assertThat($tokenParserParameter->getTokenMap(),         $this->equalTo($tokenMap->reveal()));
    }
}
