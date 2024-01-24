<?php

namespace Krzysztofzylka\MicroFramework\Libs\Log;

use DateTime;
use Exception;
use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Libs\DebugBar\DebugBar;

/**
 * Logs
 */
class Log
{

    /**
     * Session GUID
     * @var string
     */
    public static string $session;

    /**
     * Write log
     * @param string $message Log message
     * @param string $level Log level, default INFO
     * @param array $content Additional content
     * @return bool
     * @throws Exception
     */
    public static function log(string $message, string $level = 'INFO', array $content = []): bool
    {
        if (!$_ENV['LOG']) {
            return false;
        }

        if (!isset(self::$session)) {
            self::$session = Generator::uuid();
        }

        $backtrace = debug_backtrace()[1] ?? debug_backtrace()[0];
        $logPath = Kernel::getPath('logs') . '/' . date('Y_m_d') . '.log.json';
        $logContent = [
            'datetime' => DateTime::createFromFormat(
                    'U.u',
                    sprintf('%.f', microtime(true))
                )->format('Y-m-d H:i:s.u'),
            'message' => $message,
            'level' => $level,
            'content' => $content,
            'ip' => self::getClientIP(),
            'file' => $backtrace['file'] ?? null,
            'class' => $backtrace['class'] ?? null,
            'function' => $backtrace['function'] ?? null,
            'line' => $backtrace['line'] ?? null,
            'get' => $_GET,
            'session' => self::$session
        ];
        $jsonLogData = json_encode($logContent);

        if (empty(trim($jsonLogData))) {
            return false;
        }

        DebugBar::timeStart('log', 'Add log');
        DebugBar::addLogMessage($logContent, $level);
        DebugBar::timeStop('log');

        try {
            return (bool)file_put_contents($logPath, $jsonLogData . PHP_EOL, FILE_APPEND);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Retrieves the client's IP address.
     * @return ?string The client's IP address or null if not found.
     */
    private static function getClientIP(): ?string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return null;
    }

}