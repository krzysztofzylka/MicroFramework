<?php

namespace Krzysztofzylka\MicroFramework\Extension\ErrorHandler;

use DateTime;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Client;

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
            self::log(
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
     * Write log
     * @param string $message
     * @param string $level log level, default INFO
     * @param array $content
     * @return void
     */
    private static function log(string $message, string $level = 'INFO', array $content = []): void
    {
        $backtrace = debug_backtrace()[0];
        $logPath = Kernel::getPath('logs') . '/' . date('Y_m_d') . '.log.json';
        $logContent = [
            'datetime' => DateTime::createFromFormat('U.u', sprintf('%.f', microtime(true)))->format('Y-m-d H:i:s.u'),
            'level' => $level,
            'message' => $message,
            'content' => $content,
            'ip' => Client::getIP(),
            'file' => $backtrace['file'],
            'class' => $backtrace['class'],
            'function' => $backtrace['function'],
            'line' => $backtrace['line'],
            'accountId' => Account::$accountId,
            'get' => $_GET
        ];

        $jsonLogData = json_encode($logContent);

        if (empty(trim($jsonLogData))) {
            return;
        }

        file_put_contents($logPath, $jsonLogData . PHP_EOL, FILE_APPEND);
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
            self::log(
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