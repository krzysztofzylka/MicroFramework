<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View {

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
    public function setController(Controller $controller) : void {
        $this->controller = $controller;
    }

    /**
     * Load view
     * @param string $name
     * @param array $variables
     * @return string
     * @throws ViewException
     */
    public function render(string $name, array $variables = []) : string {
        try {
            if (isset($this->controller)) {
                $name = $this->controller->name . DIRECTORY_SEPARATOR . $name;
            }

            $nameExplode = explode('/', $name);

            $app = [
                'controller' => $this->controller,
                'view' => $this,
                'name' => end($nameExplode),
                'variables' => $variables
            ];

            View::$environment->addGlobal('app', $app);

            return self::$environment->render($name . '.twig', $variables);
        } catch (\Exception $exception) {
            throw new ViewException($exception->getMessage());
        }
    }

}