<?php
namespace vir;

require_once(CLASSES_DIR .'/traits.php');

class Stat {

    use BasicConstruct;

    protected $formula = null;

    public $name;
    public $value = 0;
    public $adjustment = 0;

    protected function setup() {
        $this->allowed_construct_vars = ['name', 'value', 'adjustment', 'formula'];
    }

    public function calculate(FormulaParser &$formulaParser) {
        $formulaParser->compute($this->formula, $this);

    }

    public function __invoke($value = null) {
        if (is_integer($value)) {
            $this->value = $value;
        }

        return $this->value;
    }

}

class CharacterStats {

    use ObjectGroup;

    public function __construct() {

        $this->container['Level'] = new Stat(['name' => 'Level', 'value' => 1]);
    }
}

class PrimaryStats {

    use ObjectGroup;

    public function __construct() {
        foreach (['Strength', 'Perception', 'Endurance', 'Charisma', 'Intelligence', 'Agility', 'Luck'] as $stat) {
            $this->container[$stat] = new Stat(['name' => $stat, 'value' => 5]);
        }
    }
}

class SecondaryStats {

    use ObjectGroup;

    public function __construct() {
        $this->container['Health'] = new Stat(['name' => 'Health', 'formula' => 'health']);
    }

    public function calculate(FormulaParser &$formulaParser) {

        foreach ($this->container as $stat) {
            $stat->calculate($formulaParser);
        }

    }

}
