<?php

namespace Krzysztofzylka\MicroFramework\App\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\File;

class public_files extends Controller
{

    /**
     * @param string ...$assetPath
     * @return void
     * @throws NotFoundException
     */
    public function assets(string ...$assetPath): void {
        $assetPath = implode('/', $assetPath);
        $assetPath = File::repairPath($assetPath);
        $assetPath = str_replace(['../', '//', '\\'], '', $assetPath);
        $assetPath = htmlspecialchars($assetPath);
        $extension = File::getExtension($assetPath);

        if (!in_array($extension, ['js', 'jsx', 'css'])) {
            throw new NotFoundException();
        }

        $path = realpath(__DIR__ . '/../../Resources/assets') . '/' . $assetPath;

        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        header("Content-length: " . filesize($path));
        header('Content-Disposition: inline; filename="' . basename($path) . '"');
        header('Content-type: ' . File::getContentType($extension));
        readfile($path);
        exit;
    }

    /**
     * Download js file from view
     * @param string $controller
     * @param string $method
     * @return void
     * @throws NotFoundException
     */
    public function js(string $controller, string $method): void
    {
        $this->layout = 'none';
        $path = Kernel::getPath('view') . '/' . htmlspecialchars($controller) . '/' . htmlspecialchars($method) . '.js';

        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        $fileName = basename($path);

        header("Content-length: " . filesize($path));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-type: text/javascript');
        readfile($path);
        exit;
    }

}