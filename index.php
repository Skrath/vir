<?php
namespace vir;

require_once('config/settings.php');
require_once('classes/character.php');

$smarty = new Smarty_Vir();

$smarty->debugging = true;

$character = new character();

$smarty->assign('character', $character);


$smarty->display('character.tpl');


var_dump($character->primary_stats);