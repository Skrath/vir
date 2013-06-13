<?php
namespace vir;

require_once(CLASSES_DIR .'/traits.php');

class Stat {

    use BasicConstruct, Named, Leveling, Calculable;


    protected function setup() {
        $this->allowed_construct_vars = ['name', 'value', 'adjustment', 'formula'];
    }

    public function __invoke($value = null) {
        if (is_integer($value)) {
            $this->value = $value;
        }

        return $this->value;
    }

}

class PrimaryStats {

    use ObjectGroup;

    public function __construct() {
        foreach (['Strength', 'Perception', 'Endurance', 'Charisma', 'Intelligence', 'Agility', 'Luck'] as $stat) {
            $this->add($stat, new Stat(['value' => 5]));
        }
    }
}

class SecondaryStats {

    use ObjectGroup;

    public function __construct() {
        $this->add('Vigor', new Stat(['formula' => 'vigor']));
        $this->add('Vigor Regen', new Stat(['formula' => 'vigor_regen']));
    }

    public function calculate(FormulaParser &$formulaParser) {

        foreach ($this->container as $stat) {
            $stat->calculate($formulaParser);
        }

    }

}
