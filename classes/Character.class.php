<?php
namespace vir;

require_once(CLASSES_DIR .'/abilities.php');
require_once(CLASSES_DIR .'/stats.php');
require_once(CLASSES_DIR .'/FormulaParser.class.php');

class Character {

    use Named, Leveling;

    public $PrimaryStats;
    public $SecondaryStats;
    public $AbilityGroups;

    private $formulaParser;

    public function __construct() {
        $this->PrimaryStats = new PrimaryStats();
        $this->SecondaryStats = new SecondaryStats();
        $this->AbilityGroups = new AbilityGroups();
        $this->formulaParser = new FormulaParser($this);

        $this->AbilityGroups->calculate($this->formulaParser);
        $this->SecondaryStats->calculate($this->formulaParser);
    }

    public function setPrimaryStat($params) {
        $success = true;
        $error_message = null;

        $this->PrimaryStats->$params['stat'] = (int)$params['value'];
        $this->AbilityGroups->calculate($this->formulaParser);

        return (['success' => $success, 'data' => $this, 'error_message' => $error_message]);
    }

    public function setMultiplePrimaryStats($params) {
        $success = true;
        $error_message = null;

        foreach ($params as $stat => $value) {
            $this->PrimaryStats->$stat = (int)$value;
        }

        $this->AbilityGroups->calculate($this->formulaParser);

        return (['success' => $success, 'data' => $this, 'error_message' => $error_message]);
    }
}