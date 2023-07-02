<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;

/**
 * Services
 */
class Service {

    /**
     * Loaded services list
     * @var array
     */
    private static array $LOADED_SERVICES = [];

    /**
     * Load service
     * @param string $mainName
     * @param bool $single
     * @return Service
     * @throws MicroFrameworkException
     */
    public static function loadService(string $mainName, bool $single = false) : Service {
        if (!$single && isset(self::$LOADED_SERVICES[$mainName])) {
            return self::$LOADED_SERVICES[$mainName];
        }

        $servicePath = realpath(Kernel::getPath('service')) . '/';
        $pathDirectory = $servicePath;
        $className = '\service\\';

        foreach (explode('_', $mainName) as $name) {
            $path = $pathDirectory . $mainName . '.php';

            try {
                if (!file_exists($path)) {
                    throw new MicroFrameworkException('Cloud load service.');
                }

                $className .= $mainName;
                $class = new $className();

                if (!$single) {
                    self::$LOADED_SERVICES[$mainName] = $class;
                }

                return $class;
            } catch (Exception) {
            }

            $className .= $name . '\\';
            $pathDirectory .= $name . '/';
        }

        throw new MicroFrameworkException('Cloud load service.');
    }

}