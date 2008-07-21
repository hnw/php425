<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once('phpParserNonTerminal.php');
require_once('phpParserPlugin.php');
require_once('phpParserTokenizer.php');
require_once('zend_language_parser-4.4.8.php');

class phpParser
{
    public static $cache_dir = null;
    public static $nodes = null;
    public static $plugins_dir = null;
    public static $tokenizer = null;

    protected static $initialized = false;
    protected static $plugins = array();

    public static function &execute($non_terminal, $nodes)
    {
        self::initialize();

        foreach (self::$plugins as $class => $rule) {
            if ($non_terminal != $rule[0]) {
                continue;
            }
            array_shift($rule);
            $condition = !$rule ||
                phpParserPlugin::checkNonTerminalCondition($nodes, $rule);
            if ($condition) {
                call_user_func(
                    array($class, 'execute'), &$non_terminal, &$nodes);
            }
        }

        $new_node = new phpParserNonTerminal();
        $new_node->setNonTerminal($non_terminal);
        $new_node->addNodes($nodes);
        self::$nodes =& $new_node;
        return $new_node;
    }

    public static function toFile($filename)
    {
        self::initialize();

        $realfile = self::realFile($filename);
        if (!$realfile) {
            return $filename;
        }

        $filename = realpath($realfile);
        $cache_file = self::createPath(self::$cache_dir, urlencode($filename));
        $condition = is_file($cache_file) &&
            (filemtime($filename) < filemtime($cache_file));
        if ($condition) {
            return $cache_file;
        }

        self::createDirecotry(self::$cache_dir);
        file_put_contents($cache_file, self::toString($filename));
        return $cache_file;
    }

    public static function &toNodes(&$source)
    {
        self::initialize();
        self::parse($source);
        return self::$nodes;
    }

    public static function &toString(&$source)
    {
        self::initialize();
        self::parse($source);
        return phpParserPlugin::treeToString(self::$nodes);
    }

    public static function &toTerminals(&$source)
    {
        self::initialize();
        self::parse($source);
        return phpParserPlugin::getTerminals(self::$nodes);
    }

    public static function &toTokens(&$source)
    {
        self::initialize();
        self::parse($source);
        return phpParserPlugin::arrayFlatten(
            phpParserPlugin::getTokenTree(self::$nodes));
    }

    public static function &toTree(&$source)
    {
        self::initialize();
        self::parse($source);
        return phpParserPlugin::getTokenTree(self::$nodes);
    }

    protected static function createPath()
    {
        return join(func_get_args(), DIRECTORY_SEPARATOR);
    }

    protected static function createDirectory($dir)
    {
        return mkdir($dir, 0777^umask(), true);
    }

    protected static function defineYyFunctions()
    {
        if (!function_exists('yylex')) {
            function &yylex()
            {
                global $yylval;
                $token = phpParser::$tokenizer->yylex();
                $yylval = phpParser::$tokenizer->yylval;
                return $token;
            }
        }

        if (!function_exists('yyerror')) {
            function yyerror()
            {
                echo "parse error!!\n";
            }
        }
    }

    protected static function initialize()
    {
        if (self::$initialized) {
            return;
        }

        if (!self::$cache_dir) {
            self::$cache_dir = self::createPath(dirname(__FILE__), 'cache');
        }

        if (!self::$plugins_dir) {
            self::$plugins_dir = self::createPath(dirname(__FILE__), 'plugins');
        }

        $suffix = '.php';
        $pattern = sprintf(
            self::createPath('%s', '*%s'), self::$plugins_dir, $suffix);
        foreach (glob($pattern) as $path) {
            require_once($path);
            $class = __CLASS__.'Plugin'.
                substr(basename($path), 0, -strlen($suffix));
            $properties = get_class_vars($class);
            $rule =& $properties['rule'];
            self::$plugins[$class] =& $rule;
        }

        self::defineYyFunctions();
    }

    protected static function parse(&$source)
    {
        global $yydebug;
        if (!self::$tokenizer) {
            self::$tokenizer = new phpParserTokenizer();
        }
        self::$tokenizer->setSource($source);
        self::$tokenizer->debug = $yydebug;
        return yyparse();
    }

    protected static function realFile($filename)
    {
        if ($filename[0] === '/') {
            return $filename;
        }

        foreach (split(':', get_include_path()) as $include_path) {
            $realfile = self::createPath($include_path, $filename);
            if (is_file($realfile)) {
                return $realfile;
            }
        }

        return null;
    }
}
