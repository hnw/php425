<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserPluginStrrpos extends phpParserPlugin
{
    public static $rule = array(
        'function_call', TT_STRING, '(', 'function_call_parameter_list', ')');

    public static function execute(&$non_terminal, &$nodes)
    {
        if ($nodes[0]->getTokenString() !== 'strrpos') {
            return;
        }

        $tmp_nodes =& $nodes[2]->getNodes();
        if (!$tmp_nodes) {
            return;
        }

        $tmp_nodes =& $tmp_nodes[0]->getNodes();
        $parameters = array();
        while (count($tmp_nodes) >= 3) {
            $parameters[] =& $tmp_nodes[2];
            array_unshift($parameters, $tmp_nodes[2]);
            $tmp_nodes =& $tmp_nodes[0]->getNodes();
        }
        array_unshift($parameters, $tmp_nodes[0]);

        $parameter =& $parameters[1];
        $tmp_nodes =& $parameter->getNodes();

        // add 'substr('
        $terminal = new phpParserTerminal();
        $terminal->setToken('substr(');
        array_unshift($tmp_nodes, $terminal);

        // add ', 0, 1)'
        $terminal = new phpParserTerminal();
        $terminal->setToken(', 0, 1)');
        array_push($tmp_nodes, $terminal);
    }
}
