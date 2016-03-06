<?php

namespace CondParse;


use CondParse\Exception\LexerException;
use CondParse\Operand\AbstractValueOperand;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Lexer */
    private $lexer;
    /** @var TokenMap */
    private $tokenMap;
    /** @var LexerTokenFactory */
    private $lexerTokenFactory;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->lexerTokenFactory = new LexerTokenFactory;
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        $this->lexer = new Lexer;
        $this->tokenMap = new TokenMap;
    }

    /**
     * @param string $conditionString
     * @param array $expectedTokenStream
     * @dataProvider conditionStringProvider
     */
    public function testGetTokenStream($conditionString, $expectedTokenStream)
    {
        $traversable = $this->lexer->getTokenStream($conditionString, $this->tokenMap);
        $actualTokenStream = iterator_to_array($traversable);


        $this->assertEquals($expectedTokenStream, $actualTokenStream);
    }

    public function testGetTokenStream_unrecognizedTokenThrowsLexerException()
    {
        $this->expectException(LexerException::class);
        iterator_to_array($this->lexer->getTokenStream('!(true && test)', $this->tokenMap));
    }

    /**
     * @param string $conditionString
     * @param $customToken
     * @param array $expectedTokenStream
     * @dataProvider customConditionStringProvider
     */
    public function testGetTokenStream_CustomTokensAreParsed($conditionString, $customToken, $expectedTokenStream)
    {
        $this->tokenMap->registerOperand($customToken['name'], $customToken['regex'], $customToken['class']);

        $traversable = $this->lexer->getTokenStream($conditionString, $this->tokenMap);
        $actualTokenStream = iterator_to_array($traversable);


        $this->assertEquals($expectedTokenStream, $actualTokenStream);
    }

    /**
     * @param string $conditionString
     * @param $customToken
     * @param callable[] $postFunctions
     * @param array $expectedTokenStream
     * @dataProvider customConditionStringProviderWithPostFunctions
     */
    public function testGetTokenStream_CustomTokensAreParsed_postFunctionsAreApplied($conditionString, $customToken, $postFunctions, $expectedTokenStream)
    {
        foreach ($postFunctions as $postFunction) {
            $this->lexer->registerPostFunction($postFunction);
        }

        $this->tokenMap->registerOperand($customToken['name'], $customToken['regex'], $customToken['class']);

        $traversable = $this->lexer->getTokenStream($conditionString, $this->tokenMap);
        $actualTokenStream = iterator_to_array($traversable);


        $this->assertEquals($expectedTokenStream, $actualTokenStream);
    }

    protected function buildLexerTokenStream($stream)
    {
        return array_map(function ($entry) {
            return $this->lexerTokenFactory->buildLexerToken($entry[0], $entry[1]);
        }, $stream);
    }

    /**
     * @return array of arrays that map a conditionString to a stream of lexer tokens
     */
    public function conditionStringProvider()
    {
        return [
            ['true', $this->buildLexerTokenStream([[TokenMap::TOKEN_TRUE, 'true']])],
            ['false', $this->buildLexerTokenStream([[TokenMap::TOKEN_FALSE, 'false']])],
            ['!(true)', $this->buildLexerTokenStream([
                [TokenMap::TOKEN_NOT, '!'],
                [TokenMap::TOKEN_BRACKET_OPEN, '('],
                [TokenMap::TOKEN_TRUE, 'true'],
                [TokenMap::TOKEN_BRACKET_CLOSE, ')'],
            ])],
            ['true && false', $this->buildLexerTokenStream([
                [TokenMap::TOKEN_TRUE, 'true'],
                [TokenMap::TOKEN_WHITESPACE, ' '],
                [TokenMap::TOKEN_AND, '&&'],
                [TokenMap::TOKEN_WHITESPACE, ' '],
                [TokenMap::TOKEN_FALSE, 'false']
            ])]
        ];
    }

    /**
     * @return array of arrays that map a conditionString and a set of Custom Tokens to a stream of lexer tokens
     */
    public function customConditionStringProvider()
    {
        return [
            ['#123# && #321#', ['name' => 'ID', 'regex' => '#\d+#', 'class' => ValueOperand::class],
                $this->buildLexerTokenStream([
                    ['ID', '#123#'],
                    [TokenMap::TOKEN_WHITESPACE, ' '],
                    [TokenMap::TOKEN_AND, '&&'],
                    [TokenMap::TOKEN_WHITESPACE, ' '],
                    ['ID', '#321#']
                ])
            ],
            [
                '(something || another)',
                ['name' => 'WORD', 'regex' => '[a-z]+', 'class' => ValueOperand::class],
                $this->buildLexerTokenStream([
                    [TokenMap::TOKEN_BRACKET_OPEN, '('],
                    ['WORD', 'something'],
                    [TokenMap::TOKEN_WHITESPACE, ' '],
                    [TokenMap::TOKEN_OR, '||' ],
                    [TokenMap::TOKEN_WHITESPACE, ' '],
                    ['WORD', 'another'],
                    [TokenMap::TOKEN_BRACKET_CLOSE, ')']
                ])
            ],
        ];
    }

    /**
     * @return array of arrays that map a conditionString and a set of Custom Tokens to a stream of lexer tokens
     */
    public function customConditionStringProviderWithPostFunctions()
    {
        return [
            [
                '#123# && #321#',
                ['name' => 'ID', 'regex' => '#\d+#', 'class' => ValueOperand::class],
                [function ($token, $match) { return $token == 'ID' ? trim($match, '#') : $match; }],
                $this->buildLexerTokenStream([
                    ['ID', '123'],
                    [TokenMap::TOKEN_WHITESPACE, ' '],
                    [TokenMap::TOKEN_AND, '&&'],
                    [TokenMap::TOKEN_WHITESPACE, ' '],
                    ['ID', '321']
                ])
            ]
        ];
    }
}

class ValueOperand extends AbstractValueOperand
{
    /** @return bool */
    function execute()
    {
        return true;
    }

    public function __toString()
    {
        
    }
}
