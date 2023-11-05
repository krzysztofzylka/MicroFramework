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
     * Render view
     * @return void
     * @throws NotFoundException
     */
    public function render(): void
    {
        DebugBar::addFrameworkMessage('Render view ' . $this->action, 'Render view');
        DebugBar::timeStart('render_view', 'Render view');
        $path = Kernel::getPath('view') . '/' . $this->action . $this->layoutExtension;

        if (!file_exists($path)) {
            throw new NotFoundException('View file not found: ' . $path);
        }

        DebugBar::timeStart('render_view_variables', 'Render view variables');
        foreach ($this->variables as $key => $value) {
            $$key = $value;
        }
        DebugBar::timeStop('render_view_variables');

        include($path);

        DebugBar::timeStop('render_view');
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

}