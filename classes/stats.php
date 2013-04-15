<?php

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

    private $stats = array();

    public function __construct() {
        foreach (array('Strength', 'Perception', 'Endurance', 'Charisma', 'Intelligence', 'Agility', 'Luck') as $stat) {
            $this->stats[$stat] = new primary_stat(array('name' => $stat));
        }
    }

    public function __get($name) {
        $name = ucfirst(strtolower($name));
        return $this->stats[$name];
    }

    public function __set($name, $value) {
        $name = ucfirst(strtolower($name));
        $this->stats[$name]($value);
    }
}

class ability_group {

    public $name;
    public $base_level;
    public $primary;
    public $secondary;
    public $negative;

}

