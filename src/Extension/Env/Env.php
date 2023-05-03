<?php

namespace Krzysztofzylka\MicroFramework\Extension\Env;

use Krzysztofzylka\MicroFramework\Kernel;

/**
 * Env
 * @package Extension\Env
 */
class Env
{

    /**
     * Create ENV instance
     * @param string $path
     * @return EnvInstance
     */
    public static function create(string $path): EnvInstance
    {
        return new EnvInstance($path);
    }

    /**
     * Load ENV from directory
     * @param string $path
     * @return void
     */
    public static function createFromDirectory(string $path): void
    {
        foreach (glob($path . '/*.env') as $path) {
            $env = self::create($path);
            $env->load();
        }
    }

}