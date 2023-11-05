<?php

namespace Krzysztofzylka\MicroFramework\Extension\Log;

use Exception;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Client;
use krzysztofzylka\SimpleLibraries\Library\Date;
use krzysztofzylka\SimpleLibraries\Library\Generator;

/**
 * Logs
 * @package Extension\Log
 */
class Log
{

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
            self::$session = Generator::guid();
        }

        $backtrace = debug_backtrace()[1];
        $logPath = Kernel::getPath('logs') . '/' . date('Y_m_d') . '.log.json';
        $logContent = [
            'datetime' => Date::getSimpleDate(true),
            'message' => $message,
            'level' => $level,
            'content' => $content,
            'ip' => Client::getIP(),
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

}