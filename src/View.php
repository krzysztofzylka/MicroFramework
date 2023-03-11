<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class View
{

    /**
     * Twig filesystem loader
     * @var FilesystemLoader
     */
    private FilesystemLoader $filesystemLoader;

    /**
     * Twig environment
     * @var Environment
     */
    private Environment $environment;

    /**
     * Controller
     * @var ?Controller
     */
    private ?Controller $controller;

    /**
     * Variables
     * @var array
     */
    private array $variables;

    /**
     * Variable name
     * @var string
     */
    private string $name;

    /**
     * Set controller object
     * @param Controller $controller
     * @return void
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * Init view
     * @throws ViewException
     */
    public function __construct()
    {
        try {
            $this->filesystemLoader = new FilesystemLoader(Kernel::getPath('view'));
            $this->filesystemLoader->addPath(__DIR__ . '/Twig/template');
            $this->filesystemLoader->addPath(__DIR__ . '/Twig/email_template');
            $this->environment = new Environment($this->filesystemLoader, ['debug' => Kernel::getConfig()->debug]);
            $this->environment->addExtension(new DebugExtension());
        } catch (Exception $exception) {
            throw new ViewException($exception->getMessage(), 500);
        }
    }

//    public function renderError(int $code, Exception $exception, string $name = 'mf_error'): string
//    {
//        $hiddenMessage = false;
//
//        if (method_exists($exception, 'getHiddenMessage')) {
//            $hiddenMessage = $exception->getHiddenMessage();
//        }
//
//        return $this->render(
//            $name,
//            [
//                'code' => $code ?? 500,
//                'debug' => Kernel::getConfig()->debug ? var_export($exception, true) : false,
//                'hiddenMessage' => $hiddenMessage
//            ]
//        );
//    }
//
    /**
     * Load view
     * @param array $variables
     * @param ?string $name
     * @return string
     * @throws ViewException
     */
    public function render(array $variables = [], ?string $name = null): string
    {
        try {
            $this->variables = $variables;

            if (isset($this->controller)) {
                $name = !str_starts_with($name, '/') ? ($this->controller->name . DIRECTORY_SEPARATOR . $name) : substr($name, 1);
            }

            $nameExplode = explode('/', $name);
            $this->name = end($nameExplode);

            if (isset($this->controller->params['admin_panel']) && $this->controller->params['admin_panel']) {
                $this->filesystemLoader->prependPath(Kernel::getPath('pa_view'));
                $this->filesystemLoader->prependPath(__DIR__ . '/AdminPanel/view');
            }

            $this->environment->addGlobal('app', $this->getGlobalVariables());

            if (Kernel::getConfig()->viewDisableCache) {
                $this->environment->setCache(false);
            }

            return $this->environment->render($name . '.twig', $variables);
        } catch (Exception $exception) {
            throw new ViewException($exception->getMessage());
        }
    }

    /**
     * Generate global variables
     * @return array
     */
    private function getGlobalVariables(): array
    {
        return [
            'name' => $this->name,
            'view' => $this,
            'variables' => $this->variables,
            'config' => (array)Kernel::getConfig(),
            'controller' => $this->controller
        ];
    }

}