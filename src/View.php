<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Form\Form;
use krzysztofzylka\SimpleLibraries\Library\Response;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

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
    private ?Controller $controller = null;

    /**
     * Variables
     * @var array
     */
    private array $variables = [];

    /**
     * Variable name
     * @var ?string
     */
    private ?string $name = null;

    /**
     * Init view
     * @throws ViewException
     */
    public function __construct()
    {
        try {
            $this->filesystemLoader = new FilesystemLoader(Kernel::getPath('view'));
            $this->filesystemLoader->addPath(__DIR__ . '/Extension/Twig/TwigFiles');
            $this->environment = new Environment($this->filesystemLoader, ['debug' => Kernel::getConfig()->debug]);
            $this->environment->addExtension(new DebugExtension());
            $translationFunction = new TwigFunction('__', function (string $name) {
                return __($name);
            });
            $this->environment->addFunction($translationFunction);
        } catch (Exception $exception) {
            throw new ViewException($exception->getMessage(), 500);
        }
    }

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
     * Render error
     * @param int $code
     * @param Exception $exception
     * @param string $name
     * @return string
     * @throws ViewException
     */
    public function renderError(int $code, Exception $exception, string $name = 'MicroFramework/Layout/error'): string
    {
        $hiddenMessage = false;

        if (method_exists($exception, 'getHiddenMessage')) {
            $hiddenMessage = $exception->getHiddenMessage();
        }

        http_response_code($code);

        if (isset(Kernel::$controllerParams['api']) && Kernel::$controllerParams['api']) {
            $data = [
                'error' => [
                    'message' => $exception->getMessage(),
                    'code' => $code
                ]
            ];

            if (Kernel::getConfig()->debug) {
                $data['error']['hiddenMessage'] = $hiddenMessage;
            }

            $response = new Response();
            $response->json($data);
        }

        return $this->render(
            [
                'code' => $code ?? 500,
                'debug' => Kernel::getConfig()->debug ? $exception : false,
                'hiddenMessage' => $hiddenMessage
            ],
            $name
        );
    }


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

            $controller = $this->controller;
            $formFunction = new TwigFunction('form', function () use ($controller) {
                return new Form($controller);
            });
            $this->environment->addFunction($formFunction);

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
        $config = [
            'name' => $this->name,
            'view' => $this,
            'variables' => $this->variables,
            'config' => (array)Kernel::getConfig(),
            'controller' => $this->controller,
            'dialogboxConfig' => '[]',
            'accountId' => Account::$accountId,
            'account' => Account::$account,
            'here' => Kernel::$url,
            'isDialogbox' => isset($_GET['dialogbox']) ? (bool)$_GET['dialogbox'] : false
        ];

        if (isset($this->controller->layout) && $this->controller->layout === 'dialogbox') {
            $config['dialogboxConfig'] = json_encode([
                'dialogboxWidth' => $this->controller->dialogboxWidth,
                'layout' => 'dialogbox',
                'title' => $this->controller->title
            ]);
        }

        return $config;
    }

}