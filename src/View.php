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

    public function renderError(int $code, Exception $exception, string $name = 'mf_error'): string
    {
        $hiddenMessage = false;

        if (method_exists($exception, 'getHiddenMessage')) {
            $hiddenMessage = $exception->getHiddenMessage();
        }

        return $this->render(
            $name,
            [
                'code' => $code ?? 500,
                'debug' => Kernel::getConfig()->debug ? var_export($exception, true) : false,
                'hiddenMessage' => $hiddenMessage
            ]
        );
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
                if (!str_starts_with($name, '/')) {
                    $name = $this->controller->name . DIRECTORY_SEPARATOR . $name;
                } else {
                    $name = substr($name, 1);
                }

                $globalVariables['controller'] = $this->controller;
            }

            $nameExplode = explode('/', $name);
            $globalVariables['name'] = end($nameExplode);

            if (isset($this->controller->params['isAdminPanel']) && $this->controller->params['isAdminPanel']) {
                View::$filesystemLoader->prependPath(Kernel::getPath('pa_view'));
                View::$filesystemLoader->prependPath(__DIR__ . '/AdminPanel/view');
            }

            if (!isset(View::$environment) || !isset(View::$filesystemLoader)) {
                Kernel::initViewVariables();
            }

            View::$environment->addGlobal('app', $globalVariables);
            View::$environment->setCache(false);
            $environment = self::$environment;
            $environment->addGlobal('app', $globalVariables);

            return $environment->render($name . '.twig', $variables);
        } catch (Exception $exception) {
            throw new ViewException($exception->getMessage());
        }
    }

}