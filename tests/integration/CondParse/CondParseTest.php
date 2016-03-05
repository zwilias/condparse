<?php


namespace CondParse;


use CondParse\Operand\AbstractLeftRightOperator;
use CondParse\Operand\AbstractValueOperand;

class CondParseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $conditionString
     * @param string $infixRepresentation
     * @dataProvider conditionStringToInfixProvider
     */
    public function testParseConditionString_usingInfixOutput($conditionString, $infixRepresentation)
    {
        $tokenMap = new TokenMap();

        $tokenMap->registerOperand('ID', '#\d+#', IdOperand::class);

        $lexer = new Lexer;
        $lexer->registerPostFunction(function ($token, $value) {
            return $token == 'ID'
                ? trim($value, '#')
                : $value;
        });


        $condParse = new CondParse($lexer, new Parser, $tokenMap);
        $executableOperand = $condParse->parseConditionString($conditionString);


        $this->assertEquals($infixRepresentation, (string) $executableOperand);
    }

    /**
     * @return array
     */
    public function conditionStringToInfixProvider()
    {
        return [
            ['true', 'TRUE'],
            ['!false', 'NOT(FALSE)'],
            ['#1# && #2#', 'AND(#1#, #2#)'],
            ['!#1# || #2#', 'OR(NOT(#1#), #2#)'],
            ['true && true || true', 'OR(AND(TRUE, TRUE), TRUE)'],
            ['((((((true))))))', 'TRUE'],
            ['((!((((true))))))', 'NOT(TRUE)'],
            [
                '(( #458# || #459# || #1016#) && #223# || !#4#) && !#727# && !#730#',
                'AND(AND(OR(AND(OR(OR(#458#, #459#), #1016#), #223#), NOT(#4#)), NOT(#727#)), NOT(#730#))'
            ]
        ];
    }

    /**
     * @dataProvider mathModeProvider
     */
    public function testParseConditionString_mathMode($string, $expected)
    {
        $tokenMap = new TokenMap;
        $tokenMap->registerOperand('NUMBER', '\d+', NumberOperand::class);
        $tokenMap->registerOperator('SUM', '\+', 5, SumOperator::class);

        $condParse = new CondParse(new Lexer, new Parser, $tokenMap);


        $this->assertThat(
            $condParse->parseConditionString($string)->execute(),
            $this->equalTo($expected)
        );
    }

    public function mathModeProvider()
    {
        return [
            ['1 + 1', 2],
            ['(5 + 1) + 3', 9],
        ];
    }
}

class SumOperator extends AbstractLeftRightOperator
{
    /** @return bool */
    function execute()
    {
        return $this->leftOperand->execute() + $this->rightOperand->execute();
    }

    /** @return string */
    function __toString()
    {
        return sprintf('SUM(%s, %s)', $this->leftOperand, $this->rightOperand);
    }
}

class NumberOperand extends AbstractValueOperand
{
    /** @return mixed */
    function execute()
    {
        return $this->value;
    }

    /** @return string */
    function __toString()
    {
        return (string) $this->value;
    }
}

class IdOperand extends AbstractValueOperand
{
    /** @return bool */
    function execute()
    {
        return true;
    }

    public function __toString()
    {
        return '#' . $this->value . '#';
    }
}
