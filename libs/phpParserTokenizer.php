<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once('phpParserTerminal.php');

class phpParserTokenizer
{
    protected static $constants = array(
        'T_CLOSE_TAG', 'T_COMMENT', 'T_DOC_COMMENT', 'T_DOUBLE_COLON',
        'T_INLINE_HTML', 'T_OPEN_TAG', 'T_WHITESPACE');

    public static function zendTokenValueToTokenValue($zend_token_value)
    {
        return constant(
            $zend_token_value == T_DOUBLE_COLON ?
                'TT_PAAMAYIM_NEKUDOTAYIM' :
                'T'.token_name($zend_token_value));
    }

    public $debug = false;
    public $source = null;
    public $tokens = array();
    public $yylval = null;

    public function __construct()
    {
        foreach (self::$constants as $constant) {
            if (!defined($constant)) define($constant, null);
        }
    }

    public function &getTerminal($terminal = null)
    {
        $token =& $this->getToken();
        if (!$terminal) {
            $terminal = new phpParserTerminal();
        }

        if (is_string($token)) {
            if ($this->debug) $this->log("STRING: $token");
            $terminal->setToken($token);
            return $terminal;
        } else if (!is_array($token)) {
            return $terminal;
        }

        switch ($token[0]) {
        case T_CLOSE_TAG:
        case T_COMMENT:
        case T_DOC_COMMENT:
        case T_INLINE_HTML:
        case T_OPEN_TAG:
        case T_WHITESPACE:
            if ($this->debug) {
                $this->log(sprintf(
                    "%s: %s\n", token_name($token[0]), $token[1]));
            }
            $function = __FUNCTION__;
            $terminal->addInvalidToken($token);
            return $this->$function($terminal);
        }

        if ($this->debug) {
            $this->log(sprintf("%s: %s\n", token_name($token[0]), $token[1]));
        }
        $terminal->setToken($token);
        return $terminal;
    }

    public function &getToken()
    {
        $token = array_shift($this->tokens);
        return $token;
    }

    public function &getTokens($force = false)
    {
        if (!$this->tokens || $force) {
            $this->tokens = token_get_all($this->source);
        }
        return $this->tokens;
    }

    public function &setSource($source)
    {
        $this->source =& $source;
        $this->getTokens(true);
        return $source;
    }

    public function yylex()
    {
        $terminal =& $this->getTerminal();
        $token =& $terminal->getToken();
        $this->yylval =& $terminal;

        if (!$token) {
            return $token;
        } else if (is_string($token)) {
            return ord($token);
        } else {
            return self::zendTokenValueToTokenValue($token[0]);
        }
    }

    protected function log($str)
    {
        echo "{$str}\n";
    }
}
