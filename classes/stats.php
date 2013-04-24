<?php
namespace vir;

require_once(CLASSES_DIR .'/traits.php');

class primary_stat {

    use BasicConstruct;

    public $name;
    public $value = 5;
    public $adjustment = 0;

    private function setup() {
        $this->allowed_construct_vars = array('name', 'value', 'adjustment');
    }

    public function __invoke($value = null) {
        if (is_integer($value)) {
            $this->value = $value;
        }

        return $this->value;
    }
}

class primary_stats {

    use ObjectGroup;

    public function __construct() {
        foreach (array('Strength', 'Perception', 'Endurance', 'Charisma', 'Intelligence', 'Agility', 'Luck') as $stat) {
            $this->container[$stat] = new primary_stat(array('name' => $stat));
        }
    }
}
