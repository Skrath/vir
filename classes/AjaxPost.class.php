<?php
namespace vir;

final class AjaxPost {

    private $sendback_JSONP;
    private $allowed_objects = array();
    private $allowed_actions = array();
    private $error_messages;
    private $output;
    private $valid = true;
    private $instantiated_object;

    public function __construct($allowed_objects, $allowed_actions) {
        $this->allowed_objects = $allowed_objects;
        $this->allowed_actions = $allowed_actions;

        // JSONP requests will send a param named 'callback'
        // If we are using JSONP we don't want to handle sessions
        // as this is assumed to be a remote AJAX request.
        //
        $this->sendback_JSONP = (isset($_REQUEST['callback']) && ($_REQUEST['callback'] != ''));

    }

    public function process() {
        $this->validate();
        $this->create_object();
        $this->run_action();
        $this->display();
    }

    private function run_action() {
        if (!$this->valid) return;

        $param_array = array();

        // Build our parameter list and make sure to pull out 'action'
        // and 'object'
        foreach ($_REQUEST as $key => $value) {
            if ($key != 'action' && $key != 'object') {
                $param_array[$key] = $value;
            }
        }

        // Call the function so long as it exists
        if (method_exists($this->instantiated_object, $this->action)) {
            $this->output = $this->instantiated_object->{$this->action}($param_array);
        } else {
            $this->post_error("{$this->action} action not found in {$this->object} class");
        }
    }

    private function create_object() {
        if (!$this->valid) return;

        // Load the necessary class file
        require_once(BASE_DIR.'/classes/' . $this->object . '.class.php');

        if (class_exists('vir\\' . $this->object)) {
            $reflectionObject = new \ReflectionClass('vir\\' . $this->object);
            if (is_null($reflectionObject->getConstructor())) {
                $this->post_error("Unable to instantiate {$this->object} object (no constructor)");
            } else {
                $this->instantiated_object = $reflectionObject->newInstance();
                // Call setup method if available
                if (method_exists($this->instantiated_object, 'setup')) {
                    $this->instantiated_object->setup();
                }
            }
        } else {
            $this->post_error("Class {$this->object} does not exist");
        }
    }

    private function post_error($message) {
        $this->error_messages[] = $message;
        $this->valid = false;
    }

    private function display() {
        $this->create_json_header();
        echo json_encode(array(
                'output' => $this->output,
                'valid' => $this->valid,
                'error_messages' => $this->error_messages,
            ));
        $this->close_json();
    }

    private function validate() {
        $this->validate_request();

        $this->validate_request_var('action');
        $this->validate_request_var('object');
    }

    private function validate_request() {
        foreach(array('action','object') as $var) {
            if ( !isset($_REQUEST[$var]) || $_REQUEST[$var] == '') {
                $this->post_error("\$_REQUEST variable is missing the $var param");
            }
        }
    }

    // Make sure that a $_REQUEST variable exists and is within our
    // $validation_array
    private function validate_request_var($variable) {
        if (!$this->valid) return;

        $validation_array_name = 'allowed_' . $variable . 's';
        if (isset($_REQUEST[$variable]) && in_array($_REQUEST[$variable], $this->$validation_array_name)) {
            $this->$variable = $_REQUEST[$variable];
        } else {
            if (isset($_REQUEST[$variable])) {
                $this->post_error("'{$_REQUEST[$variable]}' is not a valid $variable request");
            } else {
                $this->post_error("\$_REQUEST does not contain variable: $variable");
            }
        }
    }

    // Send JSONP Header if necessary
    //
    private function create_json_header() {
        if ($this->sendback_JSONP) {
            if (!DEBUGGING) {header('content-type: application/json; charset=utf-8');}
            echo $_GET['callback'] . '(';
        }
    }

    // Close the JSONP Header if necessary
    //
    private function close_json() {
        if ($this->sendback_JSONP) {
            echo ')';
        }
    }

}