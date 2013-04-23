<?php
namespace vir;

define('SMARTY_DIR', '/usr/share/php/smarty3/');

// load Smarty library
require_once(SMARTY_DIR . 'Smarty.class.php');

class Smarty_Vir extends \Smarty {

    function __construct()
    {

        // Class Constructor.
        // These automatically get set with each new instance.

        parent::__construct();

        $this->setTemplateDir(VIR_SMARTY_DIR . '/templates/');
        $this->setCompileDir(VIR_SMARTY_DIR . '/templates_c/');
        $this->setConfigDir(VIR_SMARTY_DIR . '/configs/');
        $this->setCacheDir(VIR_SMARTY_DIR . '/cache/');

        $this->caching = \Smarty::CACHING_LIFETIME_CURRENT;
        $this->assign('app_name', 'Vir');
    }

}