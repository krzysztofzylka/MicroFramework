<?php

namespace Krzysztofzylka\MicroFramework\Extension\Log;

use DateTime;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Client;

class Log
{

    /**
     * Write log
     * @param string $message Log message
     * @param string $level Log level, default INFO
     * @param array $content Additional content
     * @return bool
     */
    public static function log(string $message, string $level = 'INFO', array $content = []): bool
    {
        $backtrace = debug_backtrace()[1];
        $logPath = Kernel::getPath('logs') . '/' . date('Y_m_d') . '.log.json';
        $logContent = [
            'datetime' => self::getDatetime(),
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
            return false;
        }

        return (bool)file_put_contents($logPath, $jsonLogData . PHP_EOL, FILE_APPEND);
    }

    /**
     * Generate datetime
     * @return string
     */
    private static function getDatetime(): string
    {
        return DateTime::createFromFormat('U.u', sprintf('%.f', microtime(true)))->format('Y-m-d H:i:s.u');
    }

}