<?php
namespace vir;

trait BasicConstruct {

    protected $allowed_construct_vars = [];

    public function __construct($value_array) {
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

    public $container;

    private function formatName($name) {
        return ucfirst(strtolower($name));
    }

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
        $item->name = $name;
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

    private function NamedPreConstruct() {
        $this->addConstructVar(['name']);
    }
}

trait Leveling {
    public $level = 1;
    public $experience = 0;

    private $experience_rate = 1;
    private $experience_to_next_level = 0;

    public function increase_experience($amount) {
        for ($i = 1; $i < $this->level+1; $i++) {
            $this->experience_to_next_level += pow(100, 1 + ($i-1)/40);
        }

        $this->experience += $amount * $this->experience_rate;

        if ($this->experience >= $this->experience_to_next_level) {
            $this->level++;
            $this->increase_experience(0);
        }
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