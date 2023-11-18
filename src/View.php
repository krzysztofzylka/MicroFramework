<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * View class
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
     * @var string
     */
    private string $action;

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
     * Funkcje globalne
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
     * Render view
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
     * @param string $filePath
     * @return View
     */
    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @param string $action
     * @return self
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Add JS script
     * @param string $url
     * @return void
     */
    public static function addJsScript(string $url): void
    {
        self::$GLOBAL_VARIABLES['template']['header'] .= '<script src=\'' . $url . '\'></script>';
    }

    /**
     * Add CSS script
     * @param string $url
     * @return void
     */
    public static function addCssScript(string $url): void
    {
        self::$GLOBAL_VARIABLES['template']['header'] .= '<link rel=\'stylesheet\' href=\'' . $url . '\'>';
    }

}