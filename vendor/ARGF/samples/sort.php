<?php
require_once(dirname(__FILE__).'/../ARGF.php');
$lines =& $ARGF->toArray();
sort($lines);
foreach ($lines as $line) {
    echo $line;
}
