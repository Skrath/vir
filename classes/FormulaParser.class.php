<?php
namespace vir;

class FormulaParser {

    private $character;
    private $formulas = [];
    private $currentObject;

    public function __construct(&$character) {
        Debug::Log();

        $this->character = $character;

        $directory_contents = scandir(FORMULAS_DIR);

        foreach ($directory_contents as $file) {
            $extension = explode('.', $file);
            $extension = array_pop($extension);
            $extension = strtolower($extension);
            if ($extension == 'xml') {
                $xml = simplexml_load_file(FORMULAS_DIR . '/' . $file);

                foreach ($xml as $formula) {
                    $this->formulas[(string)$formula->attributes()['name']] = $formula;
                }
            }
        }
    }

    public function compute($formula, &$object) {
        Debug::Log();

        if (array_key_exists($formula, $this->formulas)) {
            $this->currentObject = $object;
            $xml = $this->formulas[$formula];
            foreach ($xml->children() as $child_xml) {
                $this->parse($child_xml);
            }
        }

    }

    private function parse($xml) {
        Debug::Log();

        $command = $xml->getName();

        if ( method_exists($this, $command)  ) {
            return $this->$command($xml);
        } else return (string)$xml;
    }

    private function set($xml) {
        Debug::Log();

        $var = (string)$xml->attributes()['name'];

        $value = $this->parse($xml->children());

        $cast_type = (is_null($xml->attributes()['cast'])) ? 'string' : $xml->attributes()['cast'];

        settype($value, $cast_type);

        $this->currentObject->$var = $value;
    }

    private function add($xml) {
        Debug::Log();

        $total = 0;

        foreach ($xml->children() as $element) {
            $total += (float)$this->parse($element);
        }

        return $total;
    }

    private function subtract($xml) {
        Debug::Log();

        $total = 0;

        foreach ($xml->children() as $element) {
            $total -= (float)$this->parse($element);
        }

        return $total;
    }

    private function multiply($xml) {
        Debug::Log();

        $total = 1;

        foreach ($xml->children() as $element) {
            $total *= (float)$this->parse($element);
        }

        return $total;
    }

    // Only takes 2 params currently
    private function divide($xml) {
        Debug::Log();

        $total = (float)$this->parse($xml->children()[0]) / (float)$this->parse($xml->children()[1]);

        return $total;
    }

    private function data($xml) {
        Debug::Log();

        $terms = explode('.', $xml->attributes()['type']);

        $type = array_shift($terms);

        $value = ( count(get_object_vars($xml->children())) > 0 ) ? $this->parse($xml->children()) : (string)$xml;

        $terms[] = $value;

        switch ($type) {
            case 'character':
                $value = $this->getVariable($this->character, $terms);
                break;

            case 'object':
                $value = $this->getVariable($this->currentObject, $terms);
                break;

            case 'simple':
            default:
                $value = $xml;
                break;
        }

        return $value;
    }

    // Currently only works with member vars
    private function getVariable($target, $terms) {
        Debug::Log();

        $current = $target;

        foreach ($terms as $term) {
            if (is_object($current->$term)) {
                $current = $current->$term;
            } elseif (isset($current->$term)) {
                $current = $current->$term;
            } else {
                break;
                // Some sort of message/log should be created here
            }
        }

        return is_object($current) ? $current->value : $current;
    }
}
