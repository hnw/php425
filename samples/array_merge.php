<?php
$foo = 'bar';
$bar = 'baz';
function foo() { return 'foo'; }
var_dump(array_merge(
    $foo, $bar, $bar, &$foo, foo(), array_merge('hoge', 'fuga')));
