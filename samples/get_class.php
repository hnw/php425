<?php
class Foo
{
    function Baz()
    {
        echo get_class($this)."\n";
        echo get_parent_class($this)."\n";
    }
}

class Bar extends Foo
{
}

$foo = new Foo;
$foo->baz();

$bar = new Bar;
$bar->baz();
