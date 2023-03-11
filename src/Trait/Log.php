<?php

namespace Krzysztofzylka\MicroFramework\Trait;

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
        return \Krzysztofzylka\MicroFramework\Extension\Log\Log::log($message, $level, $content);
    }

}