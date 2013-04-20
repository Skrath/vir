<?php
namespace vir;

trait BasicConstruct {

    private $allowed_construct_vars = array();

    abstract public function setup();

    public function __construct($value_array) {
        $this->setup();

        foreach ($value_array as $name => $value) {
            if (property_exists($this, $name) && in_array($name, $this->allowed_construct_vars)) {
                $this->$name = $value;
            }
        }
    }
}
