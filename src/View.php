<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Twig\Functions\Action;
use Krzysztofzylka\MicroFramework\Extension\Twig\Functions\DebugTable;
use Krzysztofzylka\MicroFramework\Extension\Twig\Functions\Form as FormTwigCustomFunctions;
use Krzysztofzylka\MicroFramework\Extension\Twig\Functions\JS;
use Krzysztofzylka\MicroFramework\Extension\Twig\Functions\Load;
use Krzysztofzylka\MicroFramework\Extension\Twig\Functions\Translate;
use krzysztofzylka\SimpleLibraries\Library\Generator;
use krzysztofzylka\SimpleLibraries\Library\Request;
use krzysztofzylka\SimpleLibraries\Library\Response;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class View
{

    /**
     * Global variables
     * @var array
     */
    public static array $globalVariables = [];

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
            $this->environment = new Environment($this->filesystemLoader, ['debug' => $_ENV['config_debug']]);
            $this->environment->addExtension(new DebugExtension());

            //add custom functions
            new Translate($this->environment);
            new Action($this->environment);
            new DebugTable($this->environment);
            new Load($this->environment);
            new JS($this->environment);

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
        http_response_code($code);
        $hiddenMessage = $exception instanceof MicroFrameworkException ? $exception->getHiddenMessage() : false;

        if (isset(Kernel::$controllerParams['api']) && Kernel::$controllerParams['api']) {
            $data = [
                'error' => [
                    'message' => $exception->getMessage(),
                    'code' => $code
                ]
            ];

            if ($_ENV['config_debug']) {
                $data['error']['hiddenMessage'] = $hiddenMessage;
            }

            $response = new Response();
            $response->json($data);
        }

        return $this->render(
            [
                'code' => $code ?? 500,
                'debug' => $_ENV['config_debug'] ? $exception : false,
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
        Debug::startTime();

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

            //add custom functions
            new FormTwigCustomFunctions($this->environment, $controller);

            if ($_ENV['config_view_cache']) {
                $this->environment->setCache(false);
            }

            $render = $this->environment->render($name . '.twig', $variables);

            if ($_ENV['config_debug']) {
                Debug::endTime('view_render_' . $name);
                Debug::$data['views'][] = [
                    'name' => $name,
                    'globalVariables' => $this->getGlobalVariables(true),
                    'variables' => $this->specialcharsarray($this->variables)
                ];
            }

            return $render;
        } catch (Exception $exception) {
            if ($this->controller) {
                throw $exception;
            }

            throw new ViewException($exception->getMessage());
        }
    }

    /**
     * Generate global variables
     * @param bool $slim
     * @return array
     */
    public function getGlobalVariables(bool $slim = false): array
    {
        $config = [
            'name' => $this->name,
            'id' => Generator::uniqId(20),
            'view' => !$slim ? $this : null,
            'variables' => !$slim ? $this->variables : null,
            'config' => !$slim ? $_ENV : null,
            'controller' => !$slim ? $this->controller : null,
            'dialogboxConfig' => '[]',
            'accountId' => Account::$accountId,
            'account' => Account::$account,
            'here' => Kernel::$url ?? null,
            'isAjax' => Request::isAjaxRequest(),
            'global' => !$slim ? self::$globalVariables : null,
            'redirectAlert' => $_SESSION['redirectAlert'] ?? null,
            'layout' => isset($this->controller->layout) ? $this->controller->layout : null
        ];

        if (isset($_SESSION['redirectAlert'])) {
            unset($_SESSION['redirectAlert']);
        }

        if (isset($this->controller->layout) && $this->controller->layout === 'dialogbox') {
            $config['dialogboxConfig'] = json_encode([
                'dialogboxWidth' => $this->controller->dialogboxWidth,
                'layout' => 'dialogbox',
                'title' => $this->controller->title
            ]);
        }

        return $config;
    }

    private function specialcharsarray(array $array)
    {
        $return = [];

        foreach ($array as $name => $value) {
            $return[$name] = is_array($value)
                ? $this->specialcharsarray($value)
                : (is_string($value) ? htmlspecialchars($value) : $value);
        }

        return $return;
    }

}