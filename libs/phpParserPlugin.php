<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class phpParserPlugin
{
    public static function &arrayFlatten(&$ary)
    {
        if (!is_array($ary)) {
            return $ary;
        }

        $function = __FUNCTION__;
        $ret = array();
        foreach ($ary as &$value) {
            if (is_array($value)) {
                $ret =& array_merge($ret, self::$function($value));
            } else {
                $ret[] =& $value;
            }
        }

        return $ret;
    }

    public static function checkNonTerminalCondition(&$node, &$condition)
    {
        if (is_array($node)) {
            $nodes =& $node;
        } else {
            $nodes =& $node->getNodes();
        }
        foreach ($nodes as $index => $tmp_node) {
            if (!isset($condition[$index])) {
                return false;
            }

            $tmp_condition = $condition[$index];
            if (get_class($tmp_node) === 'phpParserNonTerminal') {
                if ($tmp_node->getNonTerminal() === $tmp_condition) {
                    continue;
                } else {
                    return false;
                }
            }

            $token = $tmp_node->getToken();
            if (is_array($token)) {
                if (phpParserTokenizer::zendTokenValueToTokenValue($token[0])) {
                    continue;
                } else {
                    return false;
                }
            }

            if ($token != $tmp_condition) {
                return false;
            }
        }

        return true;
    }

    public static function &getNonTerminals(&$node, $options = array())
    {
        $function = __FUNCTION__;

        if (isset($options['non_terminal'])) {
            $function = __FUNCTION__.'ByNonTerminal';
            return self::$function($node, $options);
        }
    }

    public static function &getNonTerminalsByNonTerminal(&$node, $options = array())
    {
        $function = __FUNCTION__;
        $non_terminal = $options['non_terminal'];
        $condition = isset($options['condition']) ? $options['condition'] : array();

        if (get_class($node) != 'phpParserNonTerminal') {
            $null = null;
            return $ret;
        }

        $result = $node->getNonTerminal() === $non_terminal && (
            !$condition || self::checkNonTerminalCondition($node, $condition));
        if ($result) {
            return $node;
        }

        $nodes = array();
        foreach ($node->getNodes() as $tmp_node) {
            $tmp_nodes =& self::$function($tmp_node, $options);
            if (!$tmp_nodes) {
                continue;
            }
            if (is_array($tmp_nodes)) {
                $nodes = array_merge($nodes, $tmp_nodes);
            } else {
                $nodes[] = $tmp_nodes;
            }
        }
        return $nodes;
    }

    public static function &getTerminals(&$node)
    {
        if (get_class($node) === 'phpParserTerminal') {
            $ret = array(&$node);
            return $ret;
        }

        $function = __FUNCTION__;
        $terminals = array();
        $nodes =& $node->getNodes();
        foreach ($nodes as &$node) {
            $terminals = array_merge($terminals, self::$function($node));
        }
        return $terminals;
    }

    public static function &getTokenTree(&$tree)
    {
        $function = __FUNCTION__;
        $token_tree = array();
        $nodes =& $tree->getNodes();
        foreach ($nodes as &$node) {
            if (get_class($node) === 'phpParserNonTerminal') {
                $tmp_node =& self::$function($node);
            } else {
                $tmp_node =& $node->getToken();
            }

            if ($tmp_node) {
                if (is_array($tmp_node) && is_integer($tmp_node[0])) {
                    $token_tree[] =& $tmp_node[1];
                } else {
                    $token_tree[] =& $tmp_node;
                }
            }
        }

        if (count($token_tree) === 1) {
            return $token_tree[0];
        } else {
            return $token_tree;
        }
    }

    public static function &terminalsToString(&$terminals)
    {
        $tokens = array();
        foreach ($terminals as &$terminal) {
            $tokens = array_merge(
                $tokens, $terminal->getInvalidTokens(),
                array($terminal->getToken()));
        }
        return self::tokensToString($tokens);
    }

    public static function &tokensToString(&$tokens)
    {
        $str = '';
        foreach ($tokens as &$token) {
            $str .= is_array($token) ? $token[1] : $token;
        }
        return $str;
    }

    public static function &treeToString(&$tree)
    {
        return self::terminalsToString(self::getTerminals($tree));
    }
}
