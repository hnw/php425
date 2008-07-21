<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserTerminal
{
    public $invalid_tokens = array();
    public $token = null;

    public function addInvalidToken(&$token)
    {
        $this->invalid_tokens[] =& $token;
    }

    public function &getInvalidTokens()
    {
        return $this->invalid_tokens;
    }

    public function &getToken()
    {
        return $this->token;
    }

    public function &getTokenString()
    {
        if (is_array($this->token)) {
            return $this->token[1];
        } else {
            return $this->token;
        }
    }

    public function replaceTokenString($string)
    {
        if (is_array($this->token)) {
            $this->token[1] =& $string;
        } else {
            $this->token =& $string;
        }
    }

    public function setToken($token)
    {
        $this->token =& $token;
    }
}
