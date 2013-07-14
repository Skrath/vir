<?php
namespace vir;

class ObjectGroup implements \Iterator, \ArrayAccess, \JsonSerializable {
    private $position = 0;
    private $container = [];

    public function __construct() {
        Debug::Log();

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

    ///// ArrayAccess functions
    public function offsetSet($offset, $value) {
        Debug::Log();

        $value->setName($offset);
        $this->container[] = $value;
    }

    public function offsetExists($offset) {
        $item = $this->find($offset);
        return isset($item);
    }

    public function offsetUnset($offset) {
        return false;
    }

    public function offsetGet($offset) {
        return $this->find($offset);
    }

    ///// JsonSerializeable
    public function jsonSerialize() {
        return $this->container;
    }

    public function __get($name) {
        Debug::Log();

        return $this->find($name);
    }

    public function __set($name, $value) {
        Debug::Log();

        $item = $this->find($name);
        return $item($value);
    }

    private function find($name) {
        Debug::Log();

        $filter = function($array) use ($name) {
            return $array->checkName($name);
        };

        $results = array_filter($this->container, $filter);

        return array_shift($results);
    }
}