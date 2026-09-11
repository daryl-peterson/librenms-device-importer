<?php

/**
 * LibreNMS Device Importer Log Class.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use Illuminate\Support\Facades\Log as Logger;


/**
 * LibreNMS Device Importer Log Class.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class Log {
    private static array $ignoredClasses = [self::class];

    // 1. Public API Methods
    public static function info(string $message, mixed $context = []): void {
        self::writeLog('INFO', $message);
    }

    public static function debug(string $message, mixed $context = []): void {
        self::writeLog('DEBUG', $message, $context);
    }

    public static function error(string $message, mixed $context = []): void {
        self::writeLog('ERROR', $message, $context);
    }

    // 2. Centralized Writer and Tracer
    private static function writeLog(string $level, string $message, mixed $context = []): void {

        try {
            // Increase frame limit slightly since we added the internal 'writeLog' step
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6);

            $callerLine = 'unknown';
            $callerFile = 'unknown';
            $callerClass = 'Global';
            $callerFunction = 'main';

            foreach ($trace as $index => $frame) {
                $frameClass = $frame['class'] ?? null;

                if ($frameClass && in_array($frameClass, self::$ignoredClasses, true)) {
                    continue;
                }

                $triggerFrame = $trace[$index - 1] ?? null;
                $callerLine = $triggerFrame['line'] ?? 'unknown';
                $callerFile = $triggerFrame['file'] ?? 'unknown';

                $callerClass = $frameClass ?? 'Global';
                $callerFunction = $frame['function'] ?? 'main';
                break;
            }

            $logEntry = sprintf(
                "\n\nClass   : %s\nMethod  : %s\nLine    : %s\nFile    : %s\nMessage : %s\n",
                $callerClass,
                $callerFunction,
                $callerLine,
                basename($callerFile),
                $message
            );

            if (isset($context) && is_array($context) && !empty($context)) {
                $logEntry .= "\nContext : " . print_r($context, true) . "\n";
                unset($context);
            }


            if (isset($context) && is_object($context)) {
                $logEntry .= "Context : " . print_r($context, true) . "\n";
                unset($context);
            }

            if (isset($context)) {
                Logger::log($level, $logEntry, $context);
            } else {
                Logger::log($level, $logEntry);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
