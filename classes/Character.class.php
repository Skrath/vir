<?php
namespace vir;

require_once(CLASSES_DIR .'/ObjectGroup.class.php');
require_once(CLASSES_DIR .'/abilities.php');
require_once(CLASSES_DIR .'/stats.php');
require_once(CLASSES_DIR .'/FormulaParser.class.php');

class Character {

    use BasicConstruct, Named, Leveling;

    public $PrimaryStats;
    public $SecondaryStats;
    public $AbilityGroups;

    private $formulaParser;

    private function CharacterPostConstruct() {
        Debug::Log();

        $this->PrimaryStats = new PrimaryStats();
        $this->SecondaryStats = new SecondaryStats();
        $this->AbilityGroups = new AbilityGroups();
        $this->formulaParser = new FormulaParser($this);
    }

    public function setup() {
        Debug::Log();

        $this->AbilityGroups->calculate($this->formulaParser);
        $this->SecondaryStats->calculate($this->formulaParser);
    }

    public function setPrimaryStat($params) {
        Debug::Log();

        $this->PrimaryStats->$params['stat'] = (int)$params['value'];
        $this->AbilityGroups->calculate($this->formulaParser);

        return $this;
    }

    public function setMultiplePrimaryStats($params) {
        Debug::Log();

        foreach ($params as $stat => $value) {
            $this->PrimaryStats->$stat = (int)$value;
        }

        $this->AbilityGroups->calculate($this->formulaParser);

        return $this;
    }
}