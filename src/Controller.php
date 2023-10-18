<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\CommonFile\CommonFile;
use Krzysztofzylka\MicroFramework\Extension\CommonFiles\CommonFiles;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Table\Table as TableExtension;
use Krzysztofzylka\MicroFramework\Trait\Alerts;
use Krzysztofzylka\MicroFramework\Trait\Controller\Confirm;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\_Array;
use krzysztofzylka\SimpleLibraries\Library\Redirect;

/**
 * Controller
 * @package Controller
 */
class Controller
{

    use Confirm;

    use Log;
    use \Krzysztofzylka\MicroFramework\Trait\Model;
    use Alerts;

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
     * Layout<br>
     * null (default) / dialogbox / table / none
     * @var ?string
     */
    public ?string $layout = null;

    /**
     * Page title (for dialogbox)
     * @var string
     */
    public string $title = '';

    /**
     * Dialogbox width
     * @var int
     */
    public int $dialogboxWidth = 500;

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
     * View is loaded
     * @var bool
     */
    public bool $viewLoaded = false;

    /**
     * View variables
     * @var array
     */
    public array $viewVariables = [];

    /**
     * Common file
     * @var CommonFile
     */
    public CommonFile $commonFile;

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
        $this->viewLoaded = true;

        echo $view->render(array_merge($this->viewVariables, $variables), $name ?? $this->method);
    }

    /**
     * Set view variable
     * @param string $name
     * @param mixed $value
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
        if (_Array::inArrayKeys($name, $this->models)) {
            return $this->models[$name];
        }

        return trigger_error(__('micro-framework.controller.undefined_property', ['name' => $name]), E_USER_WARNING);
    }

    /**
     * Redirect
     * @param string $url
     * @return never
     */
    public function redirect(string $url): never
    {
        if (str_starts_with($url, '/')) {
            Redirect::redirect($_ENV['config_page_url'] . substr($url, 1));
        }

        Redirect::redirect($url);
    }

}