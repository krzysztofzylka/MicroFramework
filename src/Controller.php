<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Extension\ModelHelper;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Response;

/**
 * Class Controller
 */
class Controller
{

    use ModelHelper;

    /**
     * Controller name
     * @var string
     */
    public string $name;

    /**
     * Controller action
     * @var string
     */
    public string $action;

    /**
     * View variables
     * @var array
     */
    public array $viewVariables = [];

    /**
     * Response class
     * @var Response
     */
    public Response $response;

    /**
     * $_POST data
     * @var array|null
     */
    public ?array $data = null;

    /**
     * Loads a view for the current controller.
     * @param string|null $action (optional) The action name to load. If not specified, the default action will be used.
     * @return bool Returns true on success.
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public function loadView(?string $action = null): bool
    {
        DebugBar::timeStart('view_' . spl_object_hash($this), 'Load view');
        $action = $action ?? ($this->name . '/' . $this->action);

        /** @var View $view */
        $view = new $_ENV['CLASS_VIEW']();
        $view->variables = $this->viewVariables;
        $view->setAction($action);
        $view->render();

        DebugBar::timeStop('view_' . spl_object_hash($this));
        DebugBar::addFrameworkMessage($view, 'Load view');

        return true;
    }

    /**
     * Sets a value for a view variable.
     * @param string $name The name of the variable.
     * @param mixed $value The value of the variable.
     * @return void
     */
    public function set(string $name, mixed $value): void
    {
        $this->viewVariables[$name] = $value;
    }

    /**
     * Magic __get
     * @param string $name
     * @return mixed|Model
     */
    public function __get(string $name): mixed
    {
        if (in_array($name, array_keys($this->models))) {
            return $this->models[$name];
        }

        return trigger_error(
            'Undefined model',
            E_USER_WARNING
        );
    }

    /**
     * Redirects the user to the specified URL.
     * @param string $url The URL to redirect to.
     * @return never This method never returns as it terminates the script execution.
     */
    public function redirect(string $url) : never
    {
        header('location: ' . $url);

        exit;
    }

}