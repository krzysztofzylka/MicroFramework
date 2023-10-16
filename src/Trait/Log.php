<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use Krzysztofzylka\MicroFramework\Extension\Log\Log as LogExtension;

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
        return LogExtension::log($message, $level, $content);
    }

}