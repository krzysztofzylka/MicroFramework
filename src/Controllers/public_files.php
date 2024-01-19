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

        $this->response->fileContents($path, 'text/javascript');
    }

}