<?php
namespace vir;

require_once(CLASSES_DIR .'/traits.php');

class AbilityGroup {

    use BasicConstruct, Named, Calculable;

    public $base_level;
    public $primary;
    public $secondary;
    public $negative;

    public function __invoke($value = null) {
        Debug::Log();

        if (is_integer($value)) {
            $this->base_level = $value;
        }

        return $this->base_level;
    }

    private function AbilityGroupPreConstruct() {
        Debug::Log();

        $this->addConstructVar(['primary', 'secondary', 'negative']);
    }
}

class AbilityGroups extends ObjectGroup {

    public function __construct() {
        Debug::Log();

        $this->loadFromXML();
    }

    public function loadFromXML() {
        Debug::Log();

        $file = XML_DIR . '/ability_groups.xml';

        if (file_exists($file)) {
            $xml = simplexml_load_file($file);

            foreach ($xml as $group) {
                $this[(string)$group->attributes()['name']] = new AbilityGroup([
                                                                  'name' => (string)$group->attributes()['name'],
                                                                  'formula' => 'ability_group',
                                                                  'primary' => (string)$group->primary,
                                                                  'secondary' => (string)$group->secondary,
                                                                  'negative' => (string)$group->negative]);
            }
        } else {
            exit("Failed to open $file");
        }

    }

    public function calculate(formulaParser &$formulaParser) {
        Debug::Log();

        foreach ($this as $group) {
            $group->calculate($formulaParser);
        }
    }

    // Overloading ObjectGroup trait method
    private function formatName($name) {
        Debug::Log();

        return $name;
    }
}