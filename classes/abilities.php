<?php
namespace vir;

require_once('classes/traits.php');

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
        $this->loadFromXML();
    }

    public function loadFromXML() {

        $file = XML_DIR . '/ability_groups.xml';

        if (file_exists($file)) {
            $xml = simplexml_load_file($file);

            foreach ($xml as $group) {

                $this->groups[(string)$group->attributes()['name']] = new ability_group(array(
                                                                  'name' => (string)$group->attributes()['name'],
                                                                  'primary' => (string)$group->primary,
                                                                  'secondary' => (string)$group->secondary,
                                                                  'negative' => (string)$group->negative));
            }
        } else {
            exit("Failed to open $file");
        }

    }

    public function calculate(primary_stats $primary_stats) {
        foreach ($this->groups as $name => $group) {
            $group->calculate($primary_stats);
        }
    }
}