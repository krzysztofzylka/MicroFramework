<?php

namespace Krzysztofzylka\MicroFramework\Libs\Twig;

use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Libs\DebugBar\DebugBar;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

/**
 * Twig libs
 */
class Twig
{

    /**
     * Twig file system loader instance
     * @var FilesystemLoader
     */
    public FilesystemLoader $twigFileSystemLoader;

    /**
     * Twig environment instance
     * @var Environment
     */
    public Environment $twigEnvironment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->twigFileSystemLoader = new FilesystemLoader();
        $this->addPath(Kernel::getPath('view'));
        $this->twigEnvironment = new Environment($this->twigFileSystemLoader, [
            'cache' => Kernel::getPath('storage') . '/cache/twig',
        ]);
        $this->twigEnvironment->setCache(false);
        $this->loadCustomFunctions();
    }

    /**
     * Render view
     * @param string $twigFilePath
     * @param array $variables
     * @return string
     * @throws MicroFrameworkException
     */
    public function render(string $twigFilePath, array $variables = []): string
    {
        try {
            $this->addPath(__DIR__ . '/Files/');
            return $this->twigEnvironment->render($twigFilePath, $variables);
        } catch (\Throwable $throwable) {
            DebugBar::addThrowable($throwable);

            throw new MicroFrameworkException($throwable->getMessage(), $throwable->getCode() ?? 500);
        }
    }

    /**
     * @param $path
     * @return void
     * @throws LoaderError
     */
    public function addPath($path): void
    {
        $this->twigFileSystemLoader->addPath($path);
    }

    /**
     * @param $path
     * @return void
     */
    public function setPaths($path): void
    {
        $this->twigFileSystemLoader->setPaths($path);
    }

    /**
     * Load custom functions
     * @return void
     */
    private function loadCustomFunctions(): void
    {
        $directoryPath = __DIR__ . '/CustomFunctions';

        foreach (scandir($directoryPath) as $file) {
            if (File::getExtension($file) !== 'php') {
                continue;
            }

            $filePath = $directoryPath . '/' . $file;
            $class = include($filePath);
            $class->load($this->twigEnvironment);
        }
    }

}