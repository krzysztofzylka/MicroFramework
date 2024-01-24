<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Libs\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Libs\Log\Log;
use Krzysztofzylka\MicroFramework\Libs\Twig\Twig;
use Throwable;

/**
 * Class View
 *
 * This class represents a view in*/
class View
{

    /**
     * Global Variables
     * @var array
     */
    public static array $GLOBAL_VARIABLES = [
        'template' => [
            'header' => '<script src="https://cdn.tailwindcss.com"></script>'
                . PHP_EOL . '<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />'
                . PHP_EOL . '<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>',
            'body' => '',
            'footer' => ''
        ]
    ];

    /**
     * Variables
     * @var array
     */
    public array $variables = [];

    /**
     * View path
     * @var string
     */
    private string $filePath;

    /**
     * Action
     * @var ?string
     */
    private ?string $action = null;

    /**
     * Twig instance
     * @var Twig
     */
    private Twig $twig;

    /**
     * Loads a view file and renders it with the specified variables.
     * @param string $filePath The path to the view file.
     * @param array $variables The variables to pass to the view file (default: []).
     * @return void
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function simpleLoad(string $filePath, array $variables = []): void
    {
        $view = new View();
        $view->setFilePath($filePath);
        $view->variables = $variables;

        $view->render();
    }

    /**
     * Renders an error page.
     * @param Throwable $throwable The throwable that caused the error.
     * @return void
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function renderErrorPage(Throwable $throwable): void
    {
        ob_clean();
        $code = $throwable->getCode() > 0 ? $throwable->getCode() : 500;
        $message = match ($code) {
            500 => 'Internal Server Error',
            409 => 'Conflict',
            408 => 'Request Timeout',
            406 => 'Not Acceptable',
            405 => 'Method Not Allowed',
            404 => 'Not Found',
            403 => 'Forbidden',
            401 => 'Unauthorized',
            400 => 'Bad Request'
        };

        self::simpleLoad(
            __DIR__ . '/Template/error.twig',
            [
                'message' => $message,
                'error_message' => $_ENV['DEBUG'] ? $throwable->getMessage() : '',
                'code' => $code,
                'url' => $_ENV['URL'] . $_ENV['DEFAULT_CONTROLLER'] . '/' . $_ENV['DEFAULT_METHOD']
            ]
        );

        if ($_ENV['DEBUG']) {
            echo DebugBar::renderHeader() . DebugBar::render();
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->twig = new Twig();
    }

    /**
     * Renders the view
     * @return void
     * @throws NotFoundException
     * @throws MicroFrameworkException
     * @throws Exception
     */
    public function render(): void
    {
        DebugBar::timeStart('render_view_' . spl_object_hash($this), 'Render view');

        DebugBar::addFrameworkMessage([
            'filePath' => $this->filePath ?? null,
            'action' => $this->action,
            'basename' => basename($this->filePath ?? ''),
            'path' => basename($this->filePath ?? '')
        ], 'Render view');

        self::$GLOBAL_VARIABLES['action'] = $this->action;
        $path = $this->action . '.twig';
        $variables = $this->variables + ['APP' => self::$GLOBAL_VARIABLES];

        if ($this->filePath ?? false) {
            $path = basename($this->filePath);
            $this->twig->setPaths(dirname($this->filePath));
        }

        DebugBar::addViewMessage([
            'path' => $path,
            'action' => $this->action,
            'variables' => $variables
        ], 'View');

        if (!file_exists($this->filePath ?? (Kernel::getPath('view') . '/' . $path))) {
            throw new NotFoundException('View file not found: ' . $path);
        }

        try {
            echo $this->twig->render($path, $variables);
        } catch (Exception $exception) {
            Log::log('View template exception', 'ERR', ['exception' => $exception->getMessage()]);

            throw new MicroFrameworkException($exception->getMessage());
        }

        DebugBar::timeStop('render_view_' . spl_object_hash($this));
    }

    /**
     * Sets the file path.
     * @param string $filePath The file path to set.
     * @return self
     */
    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Sets the action.
     * @param string $action The action to set.
     * @return self
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Retrieves the action value.
     * @return string The action value.
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Adds a JavaScript script to the header.
     * @param string $url The URL of the JavaScript file.
     * @return void
     */
    public static function addJsScript(string $url): void
    {
        self::addToHeader("<script src='$url'></script>" . PHP_EOL);
    }

    /**
     * Adds a CSS script to the header.
     * @param string $url The URL of the CSS script.
     * @return void
     */
    public static function addCssScript(string $url): void
    {
        self::addToHeader("<link rel='stylesheet' href='$url' />" . PHP_EOL);
    }

    /**
     * Add data to the header in the global variables.
     * @param string $data The data to be added to the header.
     * @return void
     */
    public static function addToHeader(string $data): void
    {
        self::$GLOBAL_VARIABLES['template']['header'] .= $data;
    }

    /**
     * Load template
     * @param string $name
     * @param array $variables
     * @return void
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function loadTemplate(string $name, array $variables): void
    {
        $path = Kernel::getPath('template') . '/' . $name . '.twig';

        if (!file_exists($path)) {
            $path = __DIR__ . '/Template/' . $name . '.twig';
        }

        if (!file_exists($path)) {
            throw new NotFoundException('Not found ' . $name . ' template');
        }

        self::simpleLoad($path, $variables);
    }

}