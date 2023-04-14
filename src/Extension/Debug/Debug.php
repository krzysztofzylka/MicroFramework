<?php

namespace Krzysztofzylka\MicroFramework\Extension\Debug;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
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
        $table = '<table class="table table-sm"><tr><th>#</th><th>SQL</th></tr>';

        foreach (array_reverse(\krzysztofzylka\DatabaseManager\Debug::getSql()) as $id => $sql) {
            $table .= '<tr><td>' . $id . '</td><td>' . nl2br($sql) . '</td></tr>';
        }

        $table .= '</table>';

        self::$variables['sqlListTable'] = $table;
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

}