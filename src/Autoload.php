<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use krzysztofzylka\SimpleLibraries\Library\File;

class Autoload
{

    /**
     * Project path
     * @var string
     */
    private string $projectPath;

    /**
     * Autoloader
     * @param string $projectPath
     * @throws NotFoundException
     */
    public function __construct(string $projectPath)
    {
        $this->projectPath = $projectPath;

        spl_autoload_register(function ($className) {
            if (str_starts_with($className, 'controller\\pa')) {
                $fileName = str_replace('controller\\pa', '', $className);
                $path = $this->generatePaPath('controller\\' . lcfirst($fileName));
            } else {
                $path = $this->generatePath($className);
            }

            if (!file_exists($path)) {
                return;
            }

            include($path);
        });
    }

    /**
     * Generate path
     * @param $className
     * @return string
     */
    private function generatePaPath($className): string
    {
        return File::repairPath(__DIR__ . '/AdminPanel' . DIRECTORY_SEPARATOR . $className . '.php');
    }

    /**
     * Generate path
     * @param $className
     * @return string
     */
    private function generatePath($className): string
    {
        return File::repairPath($this->projectPath . DIRECTORY_SEPARATOR . $className . '.php');
    }

}