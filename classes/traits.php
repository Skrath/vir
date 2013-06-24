<?php
namespace vir;

require_once(CLASSES_DIR .'/Level.class.php');

trait BasicConstruct {

    protected $allowed_construct_vars = [];

    public function __construct($value_array = []) {
        $this->callMemberFuncs('PreConstruct');

        foreach ($value_array as $name => $value) {
            if (property_exists($this, $name) && in_array($name, $this->allowed_construct_vars)) {
                $this->$name = $value;
            }
        }
        $this->callMemberFuncs('PostConstruct');
    }

    private function addConstructVar($vars) {
        $this->allowed_construct_vars = array_merge($this->allowed_construct_vars, $vars);
    }

    private function callMemberFuncs($name) {
        foreach (array_merge([__CLASS__], class_uses($this)) as $trait) {
            $function_name = explode('\\', $trait)[1] . $name;

            if (method_exists($this, $function_name)) {
                call_user_func([$this, $function_name]);
            }
        }
    }
}

trait ObjectGroup {

    use Named;

    public $container;

    public function __get($name) {
        $name = $this->formatName($name);
        return $this->find($name);
    }

    public function __set($name, $value) {
        $name = $this->formatName($name);

        $item = $this->find($name);
        $item($value);
    }

    public function add($name, $item) {
        $item->setName($name);
        $this->container[] = $item;
    }

    private function find($name) {
        $filter = function($array) use ($name) {
            return ($array->name == $this->formatName($name));
        };

        $results = array_filter($this->container, $filter);

        return array_shift($results);
    }
}

trait Named {
    public $name = '';
    public $flat_name;

    private function NamedPreConstruct() {
        $this->addConstructVar(['name']);
    }

    private function NamedPostConstruct() {
        $this->flat_name = strtolower(str_replace(' ', '_', $this->name));
    }

    public function setName($name) {
        $this->name = $this->formatName($name);
        $this->flat_name = strtolower(str_replace(' ', '_', $this->name));
    }

    private function formatName($name) {
        return ucfirst(strtolower($name));
    }

}

trait Leveling {
    public $level;

    private function LevelingPostConstruct() {
        $this->level = new Level(['value' => 1]);
    }
}

trait Calculable {
    protected $formula = null;

    public $value = 0;
    public $adjustment = 0;

    private function CalculablePreConstruct() {
        $this->addConstructVar(['formula', 'value', 'adjustment']);
    }

    public function calculate(FormulaParser &$formulaParser) {
        $formulaParser->compute($this->formula, $this);
    }

}