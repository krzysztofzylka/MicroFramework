<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Smarty;
use SmartyException;

/**
 * View class
 */
class View
{

    /**
     * Smarty instance
     * @var Smarty
     */
    private Smarty $smarty;

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
     * Is function loaded
     * @var bool
     */
    private bool $functionLoaded = false;

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
        $this->smarty = new Smarty();
        $this->smarty->caching = false;
        $this->smarty->force_compile = true;
    }

    /**
     * Load variables
     * @param array $variables
     * @return void
     */
    public function loadVariables(array $variables): void
    {
        DebugBar::timeStart('render_view_variables', 'Render view variables');

        foreach ($variables as $key => $value) {
            $this->smarty->assign($key, $value);

            DebugBar::addViewMessage($value, $key);
        }

        DebugBar::timeStop('render_view_variables');
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

        $this->loadVariables(['APP' => self::$GLOBAL_VARIABLES]);

        $path = $this->filePath ?? (Kernel::getPath('view') . '/' . $this->action . '.tpl');

        if (!file_exists($path)) {
            throw new NotFoundException('View file not found: ' . $path);
        }

        try {
            $this->loadFunctions();
            $this->smarty->display($path);
        } catch (SmartyException $exception) {
            Log::log('Smarty exception', 'ERR', ['exception' => $exception->getMessage()]);

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
     * Get smarty instance
     * @return Smarty
     */
    public function getSmarty(): Smarty
    {
        return $this->smarty;
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
     * Load functions
     * @return void
     */
    private function loadFunctions(): void
    {
        if ($this->functionLoaded) {
            return;
        }

        $this->getSmarty()->addPluginsDir(__DIR__ . '/Extension/View/Plugins');

        $this->functionLoaded = true;
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