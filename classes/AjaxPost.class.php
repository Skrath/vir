<?php
namespace vir;

final class AjaxPost {

    private $sendback_JSONP;
    private $allowed_objects = [];
    private $allowed_actions = [];
    private $error_messages;
    private $output;
    private $valid = true;
    private $instantiated_object = null;
    private $object_method;

    public function __construct($allowed_objects, $allowed_actions) {
        $this->allowed_objects = $allowed_objects;
        $this->allowed_actions = $allowed_actions;

        set_error_handler([$this, 'errorHandler']);

        // JSONP requests will send a param named 'callback'
        // If we are using JSONP we don't want to handle sessions
        // as this is assumed to be a remote AJAX request.
        //
        $this->sendback_JSONP = (isset($_REQUEST['callback']) && ($_REQUEST['callback'] != ''));

    }

    public function errorHandler($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }

        $this->post_error('(' . $errfile . '[' . $errline . ']) ' . $errstr);
    }

    public function process() {
        $this->validate();
        $this->create_object();
        $this->run_action();
        $this->display();
    }

    private function prepare_output($results = null) {
        $success = true;
        $error_message = '';

        if (is_null($results)) $this->post_error("{$this->object}->{$this->action} did not return any results");

        $errors = Debug::getLogEntries(Debug::LOG_ERROR);

        if (count($errors) > 0) {
            $error_message = array_pop($errors);
            $success = false;
        }

        return (['success' => $success, 'data' => $results, 'error_message' => $error_message]);
    }

    private function run_action() {
        if (!$this->valid) return;

        $param_array = [];

        // Build our parameter list and make sure to pull out 'action'
        // and 'object'
        foreach ($_REQUEST as $key => $value) {
            if ($key != 'action' && $key != 'object') {
                $param_array[$key] = $value;
            }
        }

        $this->output = $this->object_method->invoke($this->instantiated_object, $param_array);
    }

    private function create_object() {
        if (!$this->valid) return;

        // Load the necessary class file
        require_once(BASE_DIR.'/classes/' . $this->object . '.class.php');

        if (class_exists('vir\\' . $this->object)) {
            $reflectionObject = new \ReflectionClass('vir\\' . $this->object);

            // Make sure the method exists before discovering it
            if ($reflectionObject->hasMethod($this->action)) {
                $this->object_method = $reflectionObject->getMethod($this->action);
            } else {
                $this->post_error("{$this->action} method not found in {$this->object} class");
            }

            // If our method isn't static, then we need to instantiate the object
            if (!$this->object_method->isStatic()) {
                if ($reflectionObject->isInstantiable()) {
                    if (is_null($reflectionObject->getConstructor())) {
                        $this->instantiated_object = $reflectionObject->newInstanceWithoutConstructor();
                    } else {
                        $this->instantiated_object = $reflectionObject->newInstance();
                    }
                } else {
                    $this->post_error("Class {$this->object} is not static and could not be instantiated");
                }
            }

            // Call setup method if available
            if ($reflectionObject->hasMethod('setup')) {
                $reflectionObject->getMethod('setup')->invoke($this->instantiated_object);
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
        echo json_encode([
                'output' => $this->output,
                'valid' => $this->valid,
                'error_messages' => $this->error_messages,
            ]);
        $this->close_json();
    }

    private function validate() {
        $this->validate_request();

        $this->validate_request_var('action');
        $this->validate_request_var('object');
    }

    private function validate_request() {
        foreach(['action','object'] as $var) {
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