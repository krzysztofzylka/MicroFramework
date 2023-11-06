<?php

namespace Krzysztofzylka\MicroFramework\Controllers;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Kernel;

/**
 * Public files controller
 */
class public_files extends Controller
{

    /**
     * Download js file from view
     * @param string $controller
     * @param string $method
     * @return void
     * @throws NotFoundException
     */
    public function js(string $controller, string $method): void
    {
        $path = Kernel::getPath('view') . '/' . htmlspecialchars($controller) . '/' . htmlspecialchars($method) . '.js';

        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        $fileName = basename($path);

        header("Content-length: " . filesize($path));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-type: text/javascript');
        die(readfile($path));
    }

    /**
     * Download vue file from view
     * @param string $controller
     * @param string $method
     * @return void
     * @throws NotFoundException
     */
    public function vue(string $controller, string $method): void
    {
        $path = Kernel::getPath('view') . '/' . htmlspecialchars($controller) . '/' . htmlspecialchars($method) . '.vue';

        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        $fileName = basename($path);

        header("Content-length: " . filesize($path));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-type: text/javascript');
        die(readfile($path));
    }

}