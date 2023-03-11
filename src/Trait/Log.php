<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use DateTime;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Client;

/**
 * Logs
 * @package Trait
 */
trait Log
{

    /**
     * Write log
     * @param string $message
     * @param string $level log level, default INFO
     * @param array $content
     * @return bool
     */
    public function log(string $message, string $level = 'INFO', array $content = []): bool
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
            return false;
        }

        return (bool)file_put_contents($logPath, $jsonLogData . PHP_EOL, FILE_APPEND);
    }

}