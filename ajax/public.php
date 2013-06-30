<?php
namespace vir;

require_once('../config/settings.php');
require_once(BASE_DIR . '/classes/AjaxPost.class.php');
require_once(CLASSES_DIR .'/Debug.class.php');


$ajax = new AjaxPost(
    array('Character', 'Debug'),
    array('setPrimaryStat', 'setMultiplePrimaryStats', 'getLogFile')
);

$ajax->process();

Debug::End();