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

    public function checkName($name) {
        return ($this->name == $this->formatName($name));
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