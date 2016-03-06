<?php


namespace CondParse;


class LexerToken
{
    /** @var string */
    private $token;
    /** @var mixed */
    private $value;

    /**
     * @param string $token
     * @param mixed $value
     */
    public function __construct($token, $value)
    {
        $this->token = $token;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isToken($token)
    {
        return $this->token === $token;
    }
}
