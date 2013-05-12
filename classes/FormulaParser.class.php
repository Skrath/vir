<?php
namespace vir;

class FormulaParser {

    private $formulas = array();
    private $currentObject;

    public function __construct() {
        $file = XML_DIR . '/formulas.xml';

        if (file_exists($file)) {
            $xml = simplexml_load_file($file);

            foreach ($xml as $formula) {
                $this->formulas[(string)$formula->attributes()['name']] = $formula;
            }
        }

    }

    public function compute($formula, $object) {

        if (array_key_exists($formula, $this->formulas)) {
            $this->currentObject = $object;
            $this->parse($this->formulas[$formula]);
        }

    }

    private function parse($xml) {

        foreach ($xml->children() as $command => $xml) {
            if (method_exists($this, $command)) {
                $this->$command($xml);
            }
        }
    }

    private function set($xml) {

        $var = (string)$xml->attributes()['name'];

        if (is_null($xml->attributes()['value'])) {

        } else {
            $value = $xml->attributes()['value'];
        }

        $cast_type = (is_null($xml->attributes()['cast'])) ? 'string' : $xml->attributes()['cast'];

        settype($value, $cast_type);

    }

}