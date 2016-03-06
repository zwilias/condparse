<?php


namespace CondParse;


use CondParse\Exception\TokenMapException;
use CondParse\Operand\AndOperator;
use CondParse\Operand\BooleanOperand;
use CondParse\Operand\NotOperand;
use CondParse\Operand\OrOperator;

class TokenMap
{
    const TOKEN_NOT = 'NOT';
    const TOKEN_AND = 'AND';
    const TOKEN_OR = 'OR';
    const TOKEN_BRACKET_OPEN = 'BRACKET_OPEN';
    const TOKEN_BRACKET_CLOSE = 'BRACKET_CLOSE';
    const TOKEN_TRUE = 'TRUE';
    const TOKEN_FALSE = 'FALSE';
    const TOKEN_WHITESPACE = 'WHITESPACE';

    const DEFAULT_TOKENS = [
        self::TOKEN_NOT => '\!',
        self::TOKEN_AND => '&&',
        self::TOKEN_OR => '\|\|',
        self::TOKEN_BRACKET_OPEN => '\(',
        self::TOKEN_BRACKET_CLOSE => '\)',
        self::TOKEN_TRUE => 'true',
        self::TOKEN_FALSE => 'false',
        self::TOKEN_WHITESPACE => '\s+'
    ];

    const DEFAULT_OPERAND_TOKENS = [
        self::TOKEN_TRUE, self::TOKEN_FALSE
    ];

    const DEFAULT_OPERATOR_PRECEDENCE = [
        self::TOKEN_BRACKET_OPEN => -1,
        self::TOKEN_BRACKET_CLOSE => -1,
        self::TOKEN_OR => 100,
        self::TOKEN_AND => 1000,
        self::TOKEN_NOT => 10000
    ];

    const DEFAULT_TOKEN_CLASSES = [
        self::TOKEN_AND => AndOperator::class,
        self::TOKEN_OR => OrOperator::class,
        self::TOKEN_TRUE => BooleanOperand::class,
        self::TOKEN_FALSE => BooleanOperand::class,
        self::TOKEN_NOT => NotOperand::class
    ];

    private $tokenList = self::DEFAULT_TOKENS;
    private $operandTokens = self::DEFAULT_OPERAND_TOKENS;
    private $operatorPrecedence = self::DEFAULT_OPERATOR_PRECEDENCE;
    private $tokenClasses = self::DEFAULT_TOKEN_CLASSES;

    /**
     * @param string $name
     * @param string $regex
     * @param string $class
     */
    public function registerOperand($name, $regex, $class)
    {
        $this->registerToken($name, $regex, $class);
        $this->operandTokens[] = $name;
    }

    /**
     * @param string $name
     * @param string $regex
     * @param int $precedence
     * @param string $class
     */
    public function registerOperator($name, $regex, $precedence, $class)
    {
        $this->registerToken($name, $regex, $class);
        $this->operatorPrecedence[$name] = $precedence;
    }

    /**
     * @param string $name
     * @param string $regex
     * @param string $class
     * @throws TokenMapException
     */
    protected function registerToken($name, $regex, $class)
    {
        if (! is_string($name)) {
            throw new TokenMapException('Can\'t register token, name must be string');
        }

        if (isset($this->tokenList[$name])) {
            throw new TokenMapException(sprintf(
                'Token <%s> already defined with regex <%s>',
                $name, $this->tokenList[$name]
            ));
        }

        if (! is_subclass_of($class, OperandInterface::class)) {
            throw new TokenMapException(sprintf(
                'Token <%s> with class <%s> must implement OperandInterface', $name, $class
            ));
        }

        $this->tokenList[$name] = $regex;
        $this->tokenClasses[$name] = $class;
    }

    /**
     * @return array<string, string>
     */
    public function getTokens()
    {
        return $this->tokenList;
    }

    /**
     * @param string $leftOperator
     * @param string $rightOperator
     * @return int
     */
    public function compareOperatorPrecedence($leftOperator, $rightOperator)
    {
        return $this->operatorPrecedence[$leftOperator] - $this->operatorPrecedence[$rightOperator];
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isOperand($token)
    {
        return in_array($token, $this->operandTokens);
    }

    /**
     * @param string $token
     * @param mixed $value
     * @return OperandInterface
     */
    public function buildOperand($token, $value)
    {
        return new $this->tokenClasses[$token]($token, $value);
    }
}
