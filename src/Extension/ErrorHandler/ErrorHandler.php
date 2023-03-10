<?php

namespace Krzysztofzylka\MicroFramework\Extension\ErrorHandler;

use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Kernel;

class ErrorHandler
{

    /**
     * Catch php errors
     * @param $type
     * @param $message
     * @param $file
     * @param $line
     */
    public static function errorHandler($type, $message, $file, $line): void
    {
        if (!(error_reporting() & $type)) {
            return;
        }

        $message = htmlspecialchars($message);
        $errorType = '';

        foreach (get_defined_constants(true)['Core'] as $name => $value) {
            if (str_starts_with($name, 'E_') && $value === $type) {
                $errorType = $name;
                break;
            }
        }

        if (!str_starts_with($file['file'] ?? $file, 'xdebug:/')) {
            Log::log(
                $message,
                'E_ERROR',
                [
                    'message' => $message,
                    'type' => $errorType,
                    'file' => $file,
                    'line' => $line,
                    'backtrace' => array_reverse(debug_backtrace())
                ]
            );
        }

        if (Kernel::getConfig()->debug) {
            echo $message;
        }
    }

    /**
     * Catch critical error
     * @return void
     */
    public static function shutdownHandler(): void
    {
        $lastError = error_get_last();

        if ($lastError === null) {
            return;
        }

        $errorType = '';

        foreach (get_defined_constants(true)['Core'] as $name => $value) {
            if (str_starts_with($name, 'E_') && $value === $lastError['type']) {
                $errorType = $name;

                break;
            }
        }

        ob_end_clean();

        if (!str_starts_with($lastError['file'], 'xdebug:/')) {
            Log::log(
                $lastError['message'],
                'E_ERROR',
                [
                    'message' => $lastError['message'],
                    'type' => $errorType,
                    'file' => $lastError['file'],
                    'line' => $lastError['line'],
                    'backtrace' => array_reverse(debug_backtrace())
                ]
            );

        }

        if (Kernel::getConfig()->debug) {
            header('Content-Type: text/html; charset=utf-8');

            ob_end_clean();
            die(json_encode($lastError));
        } else {
            die('Error');
        }
    }

}