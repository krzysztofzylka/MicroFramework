<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;

/**
 * View class
 */
class View
{

    /**
     * Controller instance
     * @var Controller
     */
    public Controller $controller;

    /**
     * Variables for view
     * @var array
     */
    public array $variables = [];

    /**
     * Action
     * @var ?string
     */
    public ?string $action;

    /**
     * Layout extension
     * @var string
     */
    public string $layoutExtension = '.phtml';

    /**
     * Custom file path
     * @var string|null
     */
    public ?string $filePath = null;

    /**
     * Header
     * @var string
     */
    public static string $header = '';

    /**
     * Body
     * @var string
     */
    public static string $body = '';

    /**
     * Footer
     * @var string
     */
    public static string $footer = '';

    /**
     * Debug id
     * @var string
     */
    private string $debugId = '';

    /**
     * Render view
     * @return void
     * @throws NotFoundException
     */
    public function render(): void
    {
        DebugBar::addFrameworkMessage('Render view ' . ($this->filePath ?? $this->action), 'Render view');
        DebugBar::timeStart('render_view_' . spl_object_hash($this), 'Render view');

        $path = $this->filePath ?? (Kernel::getPath('view') . '/' . $this->action . $this->layoutExtension);

        if (!file_exists($path)) {
            throw new NotFoundException('View file not found: ' . $path);
        }

        DebugBar::timeStart('render_view_variables', 'Render view variables');
        foreach ($this->variables as $key => $value) {
            $$key = $value;
        }
        DebugBar::timeStop('render_view_variables');

        include($path);

        DebugBar::timeStop('render_view_' . spl_object_hash($this));
        DebugBar::addFrameworkMessage($path, 'View path');
    }

    /**
     * Generate js
     * @return string
     */
    public function js(): string
    {
        return '<script src="' . $_ENV['URL'] . '/public_files/js/' . $this->action . '"></script>';
    }

    /**
     * Render action
     * @param string $controller
     * @param string $method
     * @param array $parameters
     * @return string
     * @throws NotFoundException
     * @throws \Throwable
     */
    public function renderAction(string $controller, string $method, array $parameters = []): string
    {
        DebugBar::timeStart('renderAction', 'Render action');
        ob_start();
        $router = new Route();
        $router->start($controller, $method, $parameters);
        $data = ob_get_clean();
        DebugBar::timeStop('renderAction');

        return $data;
    }

}