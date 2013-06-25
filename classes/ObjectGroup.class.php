<?php
namespace vir;

class ObjectGroup implements \Iterator {
    private $position = 0;
    private $container = [];

    public function __construct() {
        $this->position = 0;
    }

    ///// Iterator functions
    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->container[$this->position];
    }

    function key() {
        return $this->container[$this->position]->name;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->container[$this->position]);
    }

    public function __get($name) {
        return $this->find($name);
    }

    public function __set($name, $value) {
        $item = $this->find($name);
        $item($value);
    }

    public function add($name, $item) {
        $item->setName($name);
        $this->container[] = $item;
    }

    private function find($name) {
        $filter = function($array) use ($name) {
            return $array->checkName($name);
        };

        $results = array_filter($this->container, $filter);

        return array_shift($results);
    }
}