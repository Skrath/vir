<?php
namespace vir;

require_once(CLASSES_DIR .'/traits.php');

class AbilityGroup {

    use BasicConstruct, Named, Calculable;

    public $flat_name;
    public $base_level;
    public $primary;
    public $secondary;
    public $negative;

    public function __invoke($value = null) {
        if (is_integer($value)) {
            $this->base_level = $value;
        }

        return $this->base_level;
    }

    private function setup() {
        $this->allowed_construct_vars = ['name', 'primary', 'secondary', 'negative', 'formula'];
    }

    private function post_construct() {
        $this->flat_name = strtolower(str_replace(' ', '_', $this->name));
    }

}

class AbilityGroups {

    use ObjectGroup;

    public function __construct() {
        $this->loadFromXML();
    }

    public function loadFromXML() {

        $file = XML_DIR . '/ability_groups.xml';

        if (file_exists($file)) {
            $xml = simplexml_load_file($file);

            foreach ($xml as $group) {
                $this->add((string)$group->attributes()['name'], new AbilityGroup([
                            'name' => (string)$group->attributes()['name'],
                            'formula' => 'ability_group',
                            'primary' => (string)$group->primary,
                            'secondary' => (string)$group->secondary,
                            'negative' => (string)$group->negative]));
            }
        } else {
            exit("Failed to open $file");
        }

    }

    public function calculate(formulaParser &$formulaParser) {
        foreach ($this->container as $group) {
            $group->calculate($formulaParser);
        }
    }

    // Overloading ObjectGroup trait method
    private function formatName($name) {
        return $name;
    }
}