<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserPluginNewReference extends phpParserPlugin
{
    public static $rule = array(
        'expr_without_variable', 'cvar', '=', '&', T_NEW,
        'static_or_variable_string', 'ctor_arguments');

    public static function execute(&$non_terminal, &$nodes)
    {
        unset($nodes[2]);
    }
}
