<?php
namespace vir;

class FormulaParser {

    private $character;
    private $formulas = array();
    private $currentObject;

    public function __construct(&$character) {
        $this->character = $character;

        $file = XML_DIR . '/formulas.xml';

        if (file_exists($file)) {
            $xml = simplexml_load_file($file);

            foreach ($xml as $formula) {
                $this->formulas[(string)$formula->attributes()['name']] = $formula;
            }
        }

    }

    public function compute($formula, &$object) {

        if (array_key_exists($formula, $this->formulas)) {
            $this->currentObject = $object;
            $xml = $this->formulas[$formula];
            foreach ($xml->children() as $child_xml) {
                $this->parse($child_xml);
            }
        }

    }

    private function parse($xml) {
        $command = $xml->getName();

        if ( method_exists($this, $command) && ($xml->count() > 0) ) {
            return $this->$command($xml);
        } else return (string)$xml;
    }

    private function set($xml) {

        $var = (string)$xml->attributes()['name'];

        $value = $this->parse($xml->children());

        $cast_type = (is_null($xml->attributes()['cast'])) ? 'string' : $xml->attributes()['cast'];

        settype($value, $cast_type);

        $this->currentObject->$var = $value;
    }

    private function add($xml) {
        $total = 0;

        foreach ($xml->children() as $element) {
            $total += (integer)$this->parse($element);
        }

        return $total;
    }

    private function subtract($xml) {
        $total = 0;

        foreach ($xml->children() as $element) {
            $total -= (integer)$this->parse($element);
        }

        return $total;
    }

    private function multiply($xml) {
        $total = 1;

        foreach ($xml->children() as $element) {
            $total *= (integer)$this->parse($element);
        }

        return $total;
    }

    private function data($xml) {
        $type = $xml->attributes()['type'];

        $value = ( count(get_object_vars($xml->children())) > 0 ) ? $this->parse($xml->children()) : (string)$xml->data;

        switch ($type) {
            case 'simple':
                $value = $xml;
                break;

            case 'primary_stat':
                $value = $this->getPrimaryStat($value);
                break;

            default:
        }

        return $value;
    }

    private function getPrimaryStat($stat) {
        return $this->character->primary_stats->$stat->value;
    }

    private function objRef($xml) {
        $var = (string)$xml->attributes()['name'];

        return $this->currentObject->$var;
    }

}