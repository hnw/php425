<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserNonTerminal
{
    public $non_terminal = null;
    public $nodes = array();

    public function addNode(&$node)
    {
        $this->nodes[] =& $node;
    }

    public function addNodes(&$nodes)
    {
        $this->nodes = array_merge($this->nodes, $nodes);
    }

    public function &getNodes()
    {
        return $this->nodes;
    }

    public function &getNonTerminal()
    {
        return $this->non_terminal;
    }

    public function &replaceNodes(&$nodes)
    {
        $this->nodes =& $nodes;
        return $this->nodes;
    }

    public function setNonTerminal(&$non_terminal)
    {
        $this->non_terminal =& $non_terminal;
    }
}
