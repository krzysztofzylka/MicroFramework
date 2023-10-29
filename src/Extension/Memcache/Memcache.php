<?php

namespace Krzysztofzylka\MicroFramework\Extension\Memcache;

use Krzysztofzylka\MicroFramework\Extension\Log\Log as LogExtension;
use Memcached;

class Memcache
{

    /**
     * Is active
     * @var bool
     */
    public static bool $active = false;

    /**
     * Memcached instance
     * @var Memcached
     */
    public static Memcached $memcachedInstance;

    /**
     * Server ip
     * @var string
     */
    public static string $serverIp = '127.0.0.1';

    /**
     * Server port
     * @var int
     */
    public static int $serverPort = 11211;

    /**
     * Active mamcached
     * @return void
     */
    public static function run(): void
    {
        if (self::$active && class_exists('Memcached')) {
            return;
        }

        self::$memcachedInstance = new Memcached();
        $addServer = self::$memcachedInstance->addServer(self::$serverIp, self::$serverPort);

        if (!$addServer) {
            self::saveError();
        }

        self::$active = true;
    }

    /**
     * Save memcached error to log
     * @return void
     */
    private static function saveError(): void
    {
        LogExtension::log('Memcached error', 'WARNING', [
            'code' => self::$memcachedInstance->getLastErrorCode(),
            'errno' => self::$memcachedInstance->getLastErrorErrno(),
            'message' => self::$memcachedInstance->getLastErrorMessage()
        ]);
    }

    /**
     * Get data
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        if (!self::$active) {
            return false;
        }

        return self::$memcachedInstance->get($key);
    }

    /**
     * Set data
     * @param string $key
     * @param mixed $value
     * @param int $expiration expiration date in seconds
     * @return bool
     */
    public static function set(string $key, mixed $value, int $expiration = 0): bool
    {
        if (!self::$active) {
            return false;
        }

        return self::$memcachedInstance->set($key, $value, $expiration);
    }

}