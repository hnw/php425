<?php
require_once(dirname(__FILE__).'/../ARGF.php');

$ARGC =& $ARGF->ARGC();
$ARGV =& $ARGF->ARGV();

$options_n = false;
if ($ARGC > 1 && $ARGV[1] === '-n') {
    $ARGC -= 1;
    unset($ARGV[1]);
    $ARGV = array_values($ARGV);
    $options_n = true;
}

while ($line =& $ARGF->each()) {
    echo ($options_n ? sprintf('%4d: ', $ARGF->lineno) : '').$line;
}
