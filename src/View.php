<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{

    /**
     * Twig FileSystemLoader
     * @var FilesystemLoader
     */
    public static FilesystemLoader $filesystemLoader;

    /**
     * Twig Environment
     * @var Environment
     */
    public static Environment $environment;

    /**
     * Controller
     * @var Controller
     */
    private Controller $controller;

    /**
     * Set controller
     * @param Controller $controller
     * @return void
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * Load view
     * @param string $name
     * @param array $variables
     * @return string
     * @throws ViewException
     */
    public function render(string $name, array $variables = []): string
    {
        try {
            $globalVariables = [
                'view' => $this,
                'variables' => $variables
            ];

            if (isset($this->controller)) {
                $name = $this->controller->name . DIRECTORY_SEPARATOR . $name;
                $globalVariables['controller'] = $this->controller;
            }

            $nameExplode = explode('/', $name);
            $globalVariables['name'] = end($nameExplode);

            if (!isset(View::$environment) || !isset(View::$filesystemLoader)) {
                Kernel::initViewVariables();
            }

            View::$environment->addGlobal('app', $globalVariables);
            View::$environment->setCache(false);

            return self::$environment->render($name . '.twig', $variables);
        } catch (Exception $exception) {
            throw new ViewException($exception->getMessage());
        }
    }

}