<?php
namespace vir;

trait BasicConstruct {

    abstract public function setup();

    protected $allowed_construct_vars = array();

    public function __construct($value_array) {
        $this->setup();

        foreach ($value_array as $name => $value) {
            if (property_exists($this, $name) && in_array($name, $this->allowed_construct_vars)) {
                $this->$name = $value;
            }
        }

        $this->post_construct();
    }

    function post_construct() {

    }
}

trait ObjectGroup {

    public $container;

    private function formatName($name) {
        return ucfirst(strtolower($name));
    }

    public function __get($name) {
        $name = $this->formatName($name);
        return $this->container[$name];
    }

    public function __set($name, $value) {
        $name = $this->formatName($name);
        $this->container[$name]($value);
    }

}