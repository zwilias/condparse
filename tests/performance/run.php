<?php

require_once "../../vendor/autoload.php";

use CondParse\CondParse;
use CondParse\Lexer;
use CondParse\Parser;
use CondParse\TokenMap;

$testData = file('./data/testconditions.csv');

class ExecuteNumber
{
    public static function execute($number)
    {
        return $number % 2 == 0;
    }
}

class NumberOperator extends \CondParse\Operand\AbstractValueOperand
{
    /** @return mixed */
    function execute()
    {
        return ExecuteNumber::execute($this->value);
    }

    /** @return string */
    function __toString()
    {
        return sprintf('number(%d)', $this->value);
    }
}

function executeWithEval($conditionString) {
    $conditionIDs = [];
    $id = '';
    for ($i = 0, $c = strlen($conditionString); $i < $c; ++$i) {
        $token = $conditionString[$i];

        if(ctype_digit($token)) {
            //token is part of the conditionid
            $id .= $token;
        } elseif ($id !== '' && $token === '#' ) {
            //the #starts and stops the id
            $conditionIDs[] = $id;
            $id = '';
        }
    }

    $parsed = $conditionString;
    foreach($conditionIDs as $id) {
        $parsed = str_replace("#$id#",' ExecuteNumber::execute('.$id.') ',$parsed);
    }

    return eval('return '.trim($parsed).';');
}

function executeWithCondParse(\CondParse\CondParse $condParse, $conditionString) {
    try {
        return $condParse->parseConditionString($conditionString)->execute();
    } catch (Exception $ex) {
        echo $ex->getMessage(), PHP_EOL, $conditionString, PHP_EOL;
        die();
    }
}

$tokenMap = new TokenMap();

$tokenMap->registerOperand('ID', '#\d+#', NumberOperator::class);

$lexer = new Lexer;
$lexer->registerPostFunction(function ($token, $value) {
    return $token == 'ID'
        ? trim($value, '#')
        : $value;
});


$condParse = new CondParse($lexer, new Parser, $tokenMap);

$resultsEval = [];
$resultsCondParse = [];


$start = microtime(true);
foreach ($testData as $conditionString) {
    $resultsEval[] = executeWithEval($conditionString);
}
$end = microtime(true);

echo "Eval: ", ($end-$start), PHP_EOL;

$start = microtime(true);
foreach ($testData as $conditionString) {
    $resultsCondParse[] = executeWithCondParse($condParse, str_replace(' ', '', $conditionString));
}
$end = microtime(true);

echo "CondParse: ", ($end-$start), PHP_EOL;

$matches = true;
for ($i = 0, $c = count($testData); $i < $c && $matches; $i++) {
    $matches &= $resultsEval[$i] == $resultsCondParse[$i];
}

echo "Matches: " . ($matches ? 'yes' : 'no') . PHP_EOL;

