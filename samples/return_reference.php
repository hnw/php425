<?php
function &foo()
{
    return true;
}
foo();

function &bar()
{
    return ( // comment


/* bar */
true || false);
}
bar();
