<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserPluginGetClass extends phpParserPlugin
{
    public static $rule = array(
        'function_call', TT_STRING, '(', 'function_call_parameter_list', ')');

    public static function execute(&$non_terminal, &$nodes)
    {
        switch ($nodes[0]->getTokenString()) {
        case 'get_class':
        case 'get_parent_class':
            break;
        default:
            return;
        }

        $nodes[0]->replaceTokenString(
            'strtolower('.$nodes[0]->getTokenString());
        $nodes[3]->replaceTokenString(')'.$nodes[3]->getTokenString());
    }
}
