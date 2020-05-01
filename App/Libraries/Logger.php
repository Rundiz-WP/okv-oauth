<?php
/**
 * Logger class. This class will write log using WordPress system.
 * 
 * @package rundiz-oauth
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizOauth\App\Libraries;


if (!class_exists('\\RundizOauth\\App\\Libraries\\Logger')) {
    class Logger
    {


        /**
         * Write message to log file.
         * 
         * @param mixed $message The message can be any type but resource, callable will not display in the log.
         */
        public static function writeLog($message)
        {
            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                if (is_array($message) || is_object($message)) {
                    $message = print_r($message, true);
                } elseif (is_bool($message) || is_null($message) || trim($message) === '') {
                    $message = var_export($message, true) . ' (value type ' . gettype($message) . ').';
                } elseif (is_callable($message) || is_resource($message)) {
                    $message = 'value type ' . gettype($message) . '.';
                }

                $messagePrefix = 'Back trace:' . PHP_EOL;
                $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10);
                if (is_array($trace)) {
                    foreach ($trace as $eachTrace) {
                        if (isset($eachTrace['file']) && isset($eachTrace['line'])) {
                            $messagePrefix .= $eachTrace['file'] . ' : ' . $eachTrace['line'] . PHP_EOL;
                        }
                    }// endforeach;
                    unset($eachTrace);
                }
                unset($trace);

                error_log($messagePrefix . $message . PHP_EOL);
                unset($messagePrefix);
            }
        }// writeLog


    }
}
