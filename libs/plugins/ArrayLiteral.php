<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserPluginArrayLiteral extends phpParserPlugin
{
    public static $rule = array(
        'expr_without_variable', '[', 'array_pair_list', ']');

    public static function execute(&$non_terminal, &$nodes)
    {
        $nodes[0]->replaceTokenString('array(');
        $nodes[2]->replaceTokenString(')');
    }
}
