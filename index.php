<?php
namespace vir;

require_once('config/settings.php');
require_once(CLASSES_DIR .'/Character.class.php');

$smarty = new Smarty_Vir();

$smarty->debugging = true;

$character = new Character();
$character->setup();


$smarty->assign('character', $character);


$smarty->display('character.tpl');


var_dump($character->primary_stats);