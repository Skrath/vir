<?php
namespace Vir;

class Debug {

    private static $logEntries = [];
    private static $scriptStart = 0;
    private static $scriptEnd = 0;

    public static function End() {
        self::$scriptEnd = microtime(true);

        $execution_time = self::$scriptEnd - $_SERVER["REQUEST_TIME_FLOAT"];

        echo 'Script execution time: ' . $execution_time . '</br>';
    }

    public static function Log($message = null) {
        $backtrace = (isset(debug_backtrace()[1])) ? debug_backtrace()[1] : debug_backtrace()[0];
        $entry = [];

        foreach (['file', 'line', 'function', 'class', 'type', 'args'] as $key) {
            if (isset($backtrace[$key])) {
                $entry[$key] = $backtrace[$key];
            } else {
                $entry[$key] = null;
            }
        }

        $entry['message'] = $message;
        $entry['timestamp'] = time();

        self::$logEntries[] = $entry;
    }

    private static function formatLogEntry($entry) {

        $format = '%s %s(%d) %s%s%s %s';
        $formatted_date = date('Y-m-d h:i:s', $entry['timestamp']);

        $formatted_text = sprintf($format,
                          $formatted_date,
                          $entry['file'],
                          $entry['line'],
                          $entry['class'],
                          $entry['type'],
                          $entry['function'],
                          $entry['message']);

        return $formatted_text;

    }

    public static function printLogEntries() {
        foreach (self::getLogEntries() as $entry) {
            echo $entry . '<br/>';
        }
    }

    public static function getLogEntries() {
        $return_array = [];

        foreach (self::$logEntries as $entry) {
            $return_array[] = self::formatLogEntry($entry);
        }

        return $return_array;
    }
}