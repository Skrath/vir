<?php
namespace vir;

define('BASE_DIR', __DIR__);

require_once('config/settings.php');
require_once('classes/character.php');

$smarty = new Smarty_Vir();

$smarty->debugging = true;
$smarty->display('character.tpl');

$character = new character();
