<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class ARGF
{
    var $fp = null;
    var $filename = null;
    var $lineno = 0;

    var $argc = null;
    var $argv = null;
    var $stdin = true;

    function ARGF($argv = null)
    {
        if (is_array($argv)) {
            array_unshift($argv, null);
            $this->argv =& $argv;
        } else {
            $this->argc =& $this->ARGC();
            $this->argv =& $this->ARGV();
        }
    }

    function &ARGC()
    {
        global $argc;
        if ($argc) {
            return $argc;
        }

        if (isset($_SERVER['argc'])) {
            return $_SERVER['argc'];
        }

    	if (isset($GLOBALS['HTTP_SERVER_VARS']['argc'])) {
            return $GLOBALS['HTTP_SERVER_VARS']['argc'];
        }

        $result = 0;
        return $result;
    }

    function &ARGV()
    {
        global $argv;
        if (is_array($argv)) {
            return $argv;
        }

        if (@is_array($_SERVER['argv'])) {
            return $_SERVER['argv'];
        }

    	if (@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
            return $GLOBALS['HTTP_SERVER_VARS']['argv'];
        }

        $result = array();
        return $result;
    }

    function initializeStream()
    {
        if ($this->fp === false) {
            return false;
        }

        if ($this->fp === null) {
            $this->nextFile();
        }

        return $this->fp;
    }

    function &each()
    {
        if (!($result = $this->initializeStream())) {
            return $result;
        }

        while ($this->fp && !($line = fgets($this->fp))) {
            $this->nextFile();
        }

        if ($line) {
            $this->lineno++;
        }

        return $line;
    }

    function eachByte()
    {
        if (!($result = $this->initializeStream())) {
            return $result;
        }

        while ($this->fp && !($c = fgetc($this->fp))) {
            $this->nextFile();
        }

        if ($c === "\n") {
            $this->lineno++;
        }

        return $c;
    }

    function &getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $class = __CLASS__;
            $instance = new $class();
        }
        return $instance;
    }

    function nextFile()
    {
        if (isset($this->argv[1])) {
            $this->argc--;
            list($this->filename) = array_splice($this->argv, 1, 1);
            $this->fp = fopen($this->filename, 'r');
            $this->stdin = false;
        } else if ($this->stdin) {
            $this->fp = STDIN;
            $this->stdin = false;
        } else {
            $this->fp = false;
        }
    }

    function &read($length)
    {
        if (!($result = $this->initializeStream())) {
            return $result;
        }

        $data = '';
        $length_org = $length;
        do {
            $string = fread($this->fp, $length);
            $length -= strlen($string);
            $data .= $string;
            if (strlen($data) >= $length_org) {
                break;
            }
            $this->nextFile();
        } while ($this->fp);

        return $data;
    }

    function &toArray()
    {
        $array = array();
        while ($line =& $this->each()) {
            $array[] =& $line;
        }
        return $array;
    }

    function &toString()
    {
        $data = '';
        while ($string = $this->read(4096)) {
            $data .= $string;
        }
        return $data;
    }
}


$ARGF = new ARGF();
$ARGC =& $ARGF->ARGC();
$ARGV =& $ARGF->ARGV();

if (debug_backtrace()) {
    return;
}

/*
 * sample code
 */
while ($line = $ARGF->each()) {
    echo $ARGF->lineno.": $line";
}
