<?php
namespace vir;

require_once('classes/character.php');

define('BASE_DIR', __DIR__);
define('XML_DIR', BASE_DIR . '/xml');



$character = new character();

/* $character->ability_groups->loadFromXML(); */

var_dump($character->ability_groups);

//echo $stats->strength;
