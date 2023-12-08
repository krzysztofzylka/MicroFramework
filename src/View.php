<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

/**
 * Class View
 *
 * The View class is responsible for rendering views with the help of the Twig templating engine.
 */
class View
{

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
     * Twig file system loader instance
     * @var FilesystemLoader
     */
    public FilesystemLoader $twigFileSystemLoader;

    /**
     * Twig environment instance
     * @var Environment
     */
    public Environment $twigEnvironment;

    /**
     * Global Variables
     * @var array
     */
    public static array $GLOBAL_VARIABLES = [
        'template' => [
            'header' => '',
            'body' => '',
            'footer' => ''
        ]
    ];

    /**
     * Constructor
     * @throws LoaderError
     */
    public function __construct()
    {
        $this->twigFileSystemLoader = new FilesystemLoader();
        $this->twigFileSystemLoader->addPath(Kernel::getPath('view'));

        $this->twigEnvironment = new Environment($this->twigFileSystemLoader, [
            'cache' => Kernel::getPath('storage') . '/cache/twig',
        ]);

        $this->twigEnvironment->setCache(false);
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
        DebugBar::addFrameworkMessage('Render view ' . ($this->filePath ?? $this->action), 'Render view');
        DebugBar::timeStart('render_view_' . spl_object_hash($this), 'Render view');
        self::$GLOBAL_VARIABLES['action'] = $this->action;

        DebugBar::addViewMessage($this->action, 'action');
        DebugBar::addViewMessage($this->variables, 'variables');
        DebugBar::addViewMessage(self::$GLOBAL_VARIABLES, 'global_variables');

        $path = $this->action . '.twig';

        if ($this->filePath ?? false) {
            $path = basename($this->filePath);
            $this->twigFileSystemLoader->setPaths(dirname($this->filePath));
        }

        if (!file_exists($this->filePath ?? (Kernel::getPath('view') . '/' . $path))) {
            throw new NotFoundException('View file not found: ' . $path);
        }

        try {
            echo $this->twigEnvironment->render($path, $this->variables + ['APP' => self::$GLOBAL_VARIABLES]);
        } catch (Exception $exception) {
            Log::log('View template exception', 'ERR', ['exception' => $exception->getMessage()]);

            throw new MicroFrameworkException($exception->getMessage());
        }

        DebugBar::timeStop('render_view_' . spl_object_hash($this));
        DebugBar::addFrameworkMessage($path, 'View path');
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
        self::addToHeader("<script src='$url'></script>");
    }

    /**
     * Adds a CSS script to the header.
     * @param string $url The URL of the CSS script.
     * @return void
     */
    public static function addCssScript(string $url): void
    {
        self::addToHeader("<link rel='stylesheet\' href='$url' />");
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

}