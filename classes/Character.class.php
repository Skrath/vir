<?php
namespace vir;

require_once(CLASSES_DIR .'/abilities.php');
require_once(CLASSES_DIR .'/stats.php');
require_once(CLASSES_DIR .'/FormulaParser.class.php');

class Character {

    public $PrimaryStats;
    public $ability_groups;

    private $formulaParser;

    public function __construct() {
        $this->PrimaryStats = new PrimaryStats();
        $this->ability_groups = new ability_groups();
        $this->formulaParser = new FormulaParser($this);

        $this->ability_groups->calculate($this->formulaParser);
    }

    public function setPrimaryStat($params) {
        $success = true;
        $error_message = null;

        $this->PrimaryStats->$params['stat'] = (int)$params['value'];
        $this->ability_groups->calculate($this->formulaParser);

        return (['success' => $success, 'data' => $this, 'error_message' => $error_message]);
    }

    public function setMultiplePrimaryStats($params) {
        $success = true;
        $error_message = null;

        foreach ($params as $stat => $value) {
            $this->PrimaryStats->$stat = (int)$value;
        }

        $this->ability_groups->calculate($this->formulaParser);

        return (['success' => $success, 'data' => $this, 'error_message' => $error_message]);
    }
}