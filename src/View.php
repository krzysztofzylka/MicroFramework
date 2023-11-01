<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use krzysztofzylka\SimpleLibraries\Library\_Array;
use krzysztofzylka\SimpleLibraries\Library\Generator;
use krzysztofzylka\SimpleLibraries\Library\Request;
use krzysztofzylka\SimpleLibraries\Library\Response;

class View
{

    /**
     * Global variables
     * @var array
     */
    public static array $globalVariables = [];

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
     * Layout extension
     * @var string
     */
    protected string $layoutExtension = 'phtml';

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
     * @throws NotFoundException
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
     * @throws NotFoundException
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

            if ($_ENV['config_debug']) {
                Debug::endTime('view_render_' . $name);
                Debug::$data['views'][] = [
                    'name' => $name,
                    'globalVariables' => $this->getGlobalVariables(true),
                    'variables' => _Array::htmlSpecialChars($this->variables)
                ];
            }

            $fullPath = Kernel::getPath('view') . '/' . $name . '.' . $this->layoutExtension;

            if (!file_exists($fullPath)) {
                throw new NotFoundException('View not found: ' . $name);
            }

            ob_start();
            include $fullPath;
            return ob_get_clean();
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
     * @throws Exception
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

    /**
     * Add javascript file
     * @return void
     */
    public function js(): void
    {
        echo '<script type="module" src="/public_files/js/' . $this->controller->name . '/' . $this->name . '"></script>';
    }

}