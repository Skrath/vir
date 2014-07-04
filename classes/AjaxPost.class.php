<?php
namespace vir;

final class AjaxPost {

    private $sendback_JSONP;
    private $allowed_objects = [];
    private $allowed_actions = [];
    private $errorMessages;
    private $output;
    private $valid = true;
    private $instantiated_object = null;
    private $object_method;

    public function __construct($allowedObjects, $allowedActions) {
        $this->allowed_objects = $allowedObjects;
        $this->allowed_actions = $allowedActions;

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

        $this->postError('(' . $errfile . '[' . $errline . ']) ' . $errstr);
    }

    public function process() {
        $this->validate();
        $this->createObject();
        $this->runAction();
        $this->display();
    }

    private function prepareOutput($results = null) {
        $success = true;
        $errorMessage = '';

        if (is_null($results)) $this->postError("{$this->object}->{$this->action} did not return any results");

        $errors = Debug::getLogEntries(Debug::LOG_ERROR);

        if (count($errors) > 0) {
            $errorMessage = array_pop($errors);
            $success = false;
        }

        return (['success' => $success, 'data' => $results, 'errorMessage' => $errorMessage]);
    }

    private function runAction() {
        if (!$this->valid) return;

        $param_array = [];

        // Build our parameter list and make sure to pull out 'action'
        // and 'object'
        foreach ($_REQUEST as $key => $value) {
            if ($key != 'action' && $key != 'object') {
                $param_array[$key] = $value;
            }
        }

        $this->output = $this->prepareOutput($this->object_method->invoke($this->instantiated_object, $param_array));
    }

    private function createObject() {
        if (!$this->valid) return;

        // Load the necessary class file
        require_once(BASE_DIR.'/classes/' . $this->object . '.class.php');

        if (class_exists('vir\\' . $this->object)) {
            $reflectionObject = new \ReflectionClass('vir\\' . $this->object);

            // Make sure the method exists before discovering it
            if ($reflectionObject->hasMethod($this->action)) {
                $this->object_method = $reflectionObject->getMethod($this->action);
            } else {
                $this->postError("{$this->action} method not found in {$this->object} class");
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
                    $this->postError("Class {$this->object} is not static and could not be instantiated");
                }
            }

            // Call setup method if available
            if ($reflectionObject->hasMethod('setup')) {
                $reflectionObject->getMethod('setup')->invoke($this->instantiated_object);
            }

        } else {
            $this->postError("Class {$this->object} does not exist");
        }
    }

    private function postError($message) {
        $this->errorMessages[] = $message;
        $this->valid = false;
    }

    private function display() {
        $this->createJsonHeader();
        echo json_encode([
                'output' => $this->output,
                'valid' => $this->valid,
                'errorMessages' => $this->errorMessages,
            ]);
        $this->closeJson();
    }

    private function validate() {
        $this->validateRequest();

        $this->validateRequestVar('action');
        $this->validateRequestVar('object');
    }

    private function validateRequest() {
        foreach(['action','object'] as $var) {
            if ( !isset($_REQUEST[$var]) || $_REQUEST[$var] == '') {
                $this->postError("\$_REQUEST variable is missing the $var param");
            }
        }
    }

    // Make sure that a $_REQUEST variable exists and is within our
    // $validation_array
    private function validateRequestVar($variable) {
        if (!$this->valid) return;

        $validation_array_name = 'allowed_' . $variable . 's';
        if (isset($_REQUEST[$variable]) && in_array($_REQUEST[$variable], $this->$validation_array_name)) {
            $this->$variable = $_REQUEST[$variable];
        } else {
            if (isset($_REQUEST[$variable])) {
                $this->postError("'{$_REQUEST[$variable]}' is not a valid $variable request");
            } else {
                $this->postError("\$_REQUEST does not contain variable: $variable");
            }
        }
    }

    // Send JSONP Header if necessary
    //
    private function createJsonHeader() {
        if ($this->sendback_JSONP) {
            if (!DEBUGGING) {header('content-type: application/json; charset=utf-8');}
            echo $_GET['callback'] . '(';
        }
    }

    // Close the JSONP Header if necessary
    //
    private function closeJson() {
        if ($this->sendback_JSONP) {
            echo ')';
        }
    }

}