<?php

namespace Krzysztofzylka\MicroFramework\Extension\Log;

use DateTime;
use Krzysztofzylka\Logger\Logger;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Client;

/**
 * Logs
 * @package Extension\Log
 */
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
            'file' => $backtrace['file'] ?? null,
            'class' => $backtrace['class'] ?? null,
            'function' => $backtrace['function'] ?? null,
            'line' => $backtrace['line'] ?? null,
            'accountId' => Account::$accountId ?? null,
            'get' => $_GET
        ];
        $jsonLogData = json_encode($logContent);

        if (empty(trim($jsonLogData))) {
            return false;
        }

        if (Kernel::getConfig()->logger) {
            $loggerContent = $logContent;
            unset($loggerContent['level'], $loggerContent['message']);
            Logger::$url = Kernel::getConfig()->loggerUrl;
            Logger::$api_key = Kernel::getConfig()->loggerApiKey;
            Logger::$site_key = Kernel::getConfig()->loggerSiteKey;
            Logger::$username = Kernel::getConfig()->loggerUsername;
            Logger::$password = Kernel::getConfig()->loggerPassword;

            Logger::log($logContent['message'], $logContent['level'], $loggerContent);
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