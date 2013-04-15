<?php

class ability_group {

    use BasicConstruct;

    public $name;
    public $base_level;
    public $primary;
    public $secondary;
    public $negative;

    private function setup() {
        $this->allowed_construct_vars = array('name', 'primary', 'secondary', 'negative');
    }

    public function calculate(primary_stats $primary_stats) {

        $this->base_level =
            (($primary_stats->{$this->primary}->value * 2) +
                ($primary_stats->{$this->secondary}->value) -
                ($primary_stats->{$this->negative}->value * 2)) -5;
    }

}

class ability_groups {
    public $groups = array();

    public function __construct() {
        //Temporary
        $this->groups['Hand to hand'] = new ability_group(array(
                                            'name' => 'Hand to Hand',
                                            'primary' => 'Agility',
                                            'secondary' => 'Intelligence',
                                            'negative' => 'Endurance'));

        $this->groups['Long Blades'] = new ability_group(array(
                                            'name' => 'Long Blades',
                                            'primary' => 'Endurance',
                                            'secondary' => 'Agility',
                                            'negative' => 'Perception'));

    }

    public function calculate(primary_stats $primary_stats) {
        foreach ($this->groups as $name => $group) {
            $group->calculate($primary_stats);
        }
    }
}