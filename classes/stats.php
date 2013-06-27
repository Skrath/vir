<?php
namespace vir;

require_once(CLASSES_DIR .'/traits.php');

class Stat {

    use BasicConstruct, Named, Leveling, Calculable;


    protected function StatPreConstruct() {

    }

    public function __invoke($value = null) {
        Debug::Log();

        if (is_integer($value)) {
            $this->value = $value;
        }

        return $this->value;
    }

}

class PrimaryStats extends ObjectGroup {

    public function __construct() {
        Debug::Log();

        foreach (['Strength', 'Perception', 'Endurance', 'Charisma', 'Intelligence', 'Agility', 'Luck'] as $stat) {
            $this->add($stat, new Stat(['value' => 5]));
        }
    }
}

class SecondaryStats extends ObjectGroup {

    public function __construct() {
        Debug::Log();

        $this->add('Vigor', new Stat(['formula' => 'vigor']));
        $this->add('Vigor Regen', new Stat(['formula' => 'vigor_regen']));
        $this->add('Focus', new Stat(['formula' => 'focus']));
        $this->add('Focus Regen', new Stat(['formula' => 'focus_regen']));
    }

    public function calculate(FormulaParser &$formulaParser) {
        Debug::Log();

        foreach ($this as $stat) {
            $stat->calculate($formulaParser);
        }

    }

}
