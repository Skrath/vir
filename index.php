<?php

require('classes/traits.php');
require('classes/stats.php');


$stats = new primary_stats();

$stats->strength->value = 8;

var_dump($stats->strength->value);

var_dump($stats);

//echo $stats->strength;

