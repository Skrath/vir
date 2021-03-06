<?php
namespace Vir;

class Debug {

    const LOG_NONE = 0;
    const LOG_DEBUG = 1;
    const LOG_INFO = 2;
    const LOG_WARN = 4;
    const LOG_ERROR = 8;
    const LOG_TRACE = 16;

    const LOG_ALL = 31;

    private static $logEntries = [];
    private static $scriptStart = 0;
    private static $scriptEnd = 0;

    public static function End() {
        self::$scriptEnd = microtime(true);

        $execution_time = self::$scriptEnd - $_SERVER["REQUEST_TIME_FLOAT"];

        self::Log('Script execution time: ' . $execution_time . '</br>', self::LOG_INFO);

        self::writeLogEntries();
    }

    public static function Log($message = null, $log_level = 16) {
        if (!($log_level & LOG_LEVEL)) return;

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
        $entry['log_level'] = $log_level;

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

    public static function getLogEntries($log_level = 31) {
        $return_array = [];

        foreach (self::$logEntries as $entry) {
            if ($log_level & $entry['log_level']) {
                $return_array[] = self::formatLogEntry($entry);
            }
        }

        return $return_array;
    }

    public static function writeLogEntries() {
       if (LOG_LEVEL == 0) return;

        $file_name = LOGS_DIR . '/' . PROCESS_ID . '.log';

        $file_pointer = fopen($file_name, 'w');

        foreach (self::getLogEntries() as $entry) {
            fwrite($file_pointer, $entry . "\n");
        }

        fclose($file_pointer);
    }

    public static function getLogFile($params) {
        $content = [];

        $file_name = LOGS_DIR . '/' . $params['id'] . '.log';

        $file_pointer = fopen($file_name, 'r');

        if ($file_pointer) {

            while ($content[] = fgets($file_pointer)) {};

            fclose($file_pointer);
        } else {
            Debug::Log("Log file ({$file_name}) not found", Debug::LOG_ERROR);
        }

        return $content;
    }
}