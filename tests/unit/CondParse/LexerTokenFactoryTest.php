<?php


namespace CondParse;

/**
 * @covers CondParse\LexerTokenFactory<extended>
 */
class LexerTokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LexerTokenFactory
     */
    private $tokenFactory;

    public function setUp()
    {
        $this->tokenFactory = new LexerTokenFactory();
    }

    /**
     * @param string $token
     * @param mixed $value
     * @dataProvider lexerTokenDataProvider
     */
    public function testBuildLexerToken_buildsLexerToken($token, $value)
    {
        $lexerToken = $this->tokenFactory->buildLexerToken($token, $value);


        $this->assertThat($lexerToken, $this->isInstanceOf(LexerToken::class));
        $this->assertThat($lexerToken->getToken(), $this->equalTo($token));
        $this->assertThat($lexerToken->getValue(), $this->equalTo($value));
    }

    public function lexerTokenDataProvider()
    {
        return [
            ['test', 'token'],
            ['other', true],
            ['a', false],
            ['what is this', 123]
        ];
    }
}
