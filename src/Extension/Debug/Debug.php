<?php

namespace Krzysztofzylka\MicroFramework\Extension\Debug;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Translation\Translation;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * Debug extension
 * @package Extension\Debug
 */
class Debug
{

    private FilesystemLoader $filesystemLoader;

    private Environment $environment;

    /**
     * Variables
     * @var array
     */
    public static array $variables = [];

    public function __construct()
    {
        try {
            $this->filesystemLoader = new FilesystemLoader(__DIR__ . '/../../Extension/Twig/TwigFiles');
            $this->environment = new Environment($this->filesystemLoader, ['debug' => true]);
            $this->environment->addExtension(new DebugExtension());
            $translationFunction = new TwigFunction('__', function (string $name) {
                return __($name);
            });
            $this->environment->addFunction($translationFunction);
            $this->environment->setCache(false);
            $view = new View();
            self::$variables['app'] = $view->getGlobalVariables();
            $this->generateSqlTable();
            $this->generateConfigTable();
            $this->generateTranslationTable();
            $this->generateKernelTable();
            self::$variables['site_load']['end'] = number_format(microtime(true) - self::$variables['site_load']['start'], 4);
        } catch (Exception $exception) {
            throw new ViewException($exception->getMessage(), 500);
        }
    }

    /**
     * Render debug
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(): string
    {
        return $this->environment->render('MicroFramework/Layout/debug.twig', self::$variables);
    }

    /**
     * SQL list table
     * @return void
     */
    private function generateSqlTable(): void
    {
        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r(array_reverse(\krzysztofzylka\DatabaseManager\Debug::getSql()));
        self::$variables['sqlListTable'] = ob_get_clean();
    }

    /**
     * Configuration table
     * @return void
     */
    private function generateConfigTable(): void
    {
        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r((array)Kernel::getConfig());
        self::$variables['configTable'] = ob_get_clean();
    }

    /**
     * Translation table
     * @return void
     */
    private function generateTranslationTable(): void
    {
        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r(Translation::$translation);
        self::$variables['translationTable'] = ob_get_clean();
    }

    /**
     * Kernel table
     * @return void
     */
    private function generateKernelTable(): void
    {
        $data = [
            'projectPath' => Kernel::getProjectPath(),
            'url' => Kernel::$url,
            'data' => Kernel::getData(),
            'paths' => Kernel::getPath(null)
        ];

        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($data);
        self::$variables['kernelTable'] = ob_get_clean();
    }

}