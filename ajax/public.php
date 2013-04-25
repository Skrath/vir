<?php
namespace vir;

require_once('../config/settings.php');
require_once(BASE_DIR . '/classes/AjaxPost.class.php');

$ajax = new AjaxPost(
    array('Character'),
    array('setPrimaryStat', 'setMultiplePrimaryStats')
);

$ajax->process();
