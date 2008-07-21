<?php
require_once(dirname(__FILE__).'/../ARGF.php');
foreach (array_reverse($ARGF->toArray()) as $line) {
    echo $line;
}
