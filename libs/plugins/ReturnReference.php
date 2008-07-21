<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserPluginReturnReference extends phpParserPlugin
{
    public static $rule = array(
        'unticked_statement', TT_RETURN, 'expr_without_variable', ';');

    public static function execute(&$non_terminal, &$nodes)
    {
        $tmp_var_name =  '$_pp_ret';

        $new_node0 = clone $nodes[0];
        $new_node0->replaceTokenString("{$tmp_var_name} =");

        $terminals =& self::getTerminals($nodes[1]);
        $new_node1 = clone $terminals[0];
        $new_node1->replaceTokenString($tmp_var_name);

        $nodes = array(
            &$new_node0, &$nodes[1], &$nodes[2], &$nodes[0], &$new_node1,
            &$nodes[2]);
    }
}
