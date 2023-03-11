<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Table\Table as TableExtension;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Redirect;

/**
 * Controller
 * @package Controller
 */
class Controller
{

    use Log;
    use \Krzysztofzylka\MicroFramework\Trait\Model;

    /**
     * Controller name
     * @var string
     */
    public string $name;

    /**
     * Method
     * @var string
     */
    public string $method;

    /**
     * Arguments
     * @var array
     */
    public array $arguments;

    /**
     * POST data
     * @var ?array
     */
    public ?array $data = null;

    /**
     * Html generator
     * @var Html
     */
    public Html $htmlGenerator;

    /**
     * Is API controller
     * @var bool
     */
    public bool $isApi = false;

    /**
     * Params
     * @var array
     */
    public array $params = [];

    /**
     * Table method
     * @var TableExtension
     */
    public TableExtension $table;

    /**
     * Load view
     * @param array $variables
     * @param ?string $name
     * @return void
     * @throws ViewException
     */
    public function loadView(array $variables = [], ?string $name = null): void
    {
        $view = new View();
        $view->setController($this);

        echo $view->render($variables, $name ?? $this->method);
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

        return trigger_error('Undefined property ' . $name, E_USER_WARNING);
    }

    /**
     * Redirect
     * @param string $url
     * @return never
     */
    public function redirect(string $url): never
    {
        if (str_starts_with($url, '/')) {
            Redirect::redirect(Kernel::getConfig()->pageUrl . substr($url, 1));
        }

        Redirect::redirect($url);
    }

}