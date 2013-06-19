<?php
namespace vir;

class FormulaParser {

    private $character;
    private $formulas = [];
    private $currentObject;

    public function __construct(&$character) {
        $this->character = $character;

        $directory_contents = scandir(FORMULAS_DIR);

        foreach ($directory_contents as $file) {
            if (strtolower(array_pop(explode('.', $file))) == 'xml') {
                $xml = simplexml_load_file(FORMULAS_DIR . '/' . $file);

                foreach ($xml as $formula) {
                    $this->formulas[(string)$formula->attributes()['name']] = $formula;
                }
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

        if ( method_exists($this, $command)  ) {
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
            $total += (float)$this->parse($element);
        }

        return $total;
    }

    private function subtract($xml) {
        $total = 0;

        foreach ($xml->children() as $element) {
            $total -= (float)$this->parse($element);
        }

        return $total;
    }

    private function multiply($xml) {
        $total = 1;

        foreach ($xml->children() as $element) {
            $total *= (float)$this->parse($element);
        }

        return $total;
    }

    // Only takes 2 params currently
    private function divide($xml) {
        $total = (float)$this->parse($xml->children()[0]) / (float)$this->parse($xml->children()[1]);

        return $total;
    }

    private function data($xml) {
        $terms = explode('.', $xml->attributes()['type']);

        $type = array_shift($terms);

        $value = ( count(get_object_vars($xml->children())) > 0 ) ? $this->parse($xml->children()) : (string)$xml;

        $terms[] = $value;

        switch ($type) {
            case 'character':
                $value = $this->getVariable($this->character, $terms);
                break;

            case 'simple':
            default:
                $value = $xml;
                break;
        }

        return $value;
    }
    
    private function getVariable($target, $terms) {
        
        $current = $target;
        
        foreach ($terms as $term) {
            if (isset($current->$term)) {
                $current = $current->$term;
            } else {
                break;
                // Some sort of message/log should be created here
            }
        }
        
        return $current;
    }

    private function getCharacterVar($value, $terms) {
        $type = array_shift($terms);

        switch ($type) {
            case 'PrimaryStat':
                $value = $this->getPrimaryStat($value);
                break;

            case 'CharacterStat':
                $value = $this->getCharacterStat($value);
                break;

            default:
                $value = $this->character->$value;
        }

        return $value;
    }

    private function getPrimaryStat($stat) {
        return $this->character->PrimaryStats->$stat->value;
    }

    private function getCharacterStat($stat) {
        return $this->character->CharacterStats->$stat->value;
    }

    private function objRef($xml) {
        $var = (string)$xml->attributes()['name'];

        return $this->currentObject->$var;
    }

}
