<?php


namespace CondParse;


/**
 * @covers CondParse\LexerToken<extended>
 */
class LexerTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testGetToken_returnsTokenFromConstructor()
    {
        $lexerToken = new LexerToken('token', 'value');


        $this->assertEquals('token', $lexerToken->getToken());
    }

    public function testGetValue_returnsValueFromConstructor()
    {
        $lexerToken = new LexerToken('token', 'value');


        $this->assertEquals('value', $lexerToken->getValue());
    }

    public function testIsToken_checksIfTokenMatches()
    {
        $lexerToken = new LexerToken('token', 'value');


        $this->assertTrue($lexerToken->isToken('token'));
        $this->assertFalse($lexerToken->isToken('random'));
    }
}
