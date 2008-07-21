<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserPluginArrayMerge extends phpParserPlugin
{
    public static $rule = array(
        'function_call', TT_STRING, '(', 'function_call_parameter_list', ')');

    public static function execute(&$non_terminal, &$nodes)
    {
        if ($nodes[0]->getTokenString() !== 'array_merge') {
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
            $tmp_nodes =& $tmp_nodes[0]->getNodes();
        }
        $parameters[] =& $tmp_nodes[0];

        foreach ($parameters as &$parameter) {
            $tokens =& self::getTerminals($parameter);
            $token =& $tokens[0];
            $token_string = $token->getTokenString();
            if ($token_string === '&') $token_string = '';
            $token->replaceTokenString('(array)'.$token_string);
        }
    }
}
