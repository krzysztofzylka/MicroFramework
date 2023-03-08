<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Table\Table as TableExtension;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Redirect;

/**
 * Controller
 * @package Controller
 */
class Controller
{

    use Log;

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
     * Models
     * @var array
     */
    public array $models = [];

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
     * Load model
     * @param string ...$name
     * @return Model
     * @throws NotFoundException
     */
    public function loadModel(string ...$name): Model
    {
        if (count($name) > 1) {
            foreach ($name as $singleName) {
                $lastModel = $this->loadModel($singleName);
            }

            return $lastModel;
        } else {
            $name = $name[0];
        }

        $startName = $name;

        if (str_starts_with($name, 'pa')) {
            $class = ObjectNameGenerator::modelPa($name);
        } else {
            $class = ObjectNameGenerator::model($name);
        }

        try {
            /** @var Model $model */
            $model = new $class();
            $model->name = $name;
            $model->controller = $this;
            $model->data = $this->data;

            if ($model->useTable && isset(DatabaseManager::$connection)) {
                $model->tableInstance = new Table($model->tableName ?? $startName);
                $model->transactionInstance = new Transaction();
            }
        } catch (Exception $exception) {
            $this->log('Fail load model ' . $name, 'ERR', ['name' => $startName, 'class' => $class, 'exception' => $exception]);

            throw new NotFoundException('Not found model ' . $startName);
        }

        $this->models[str_replace('_', '', ucwords($startName, '_'))] = $model;

        return $model;
    }

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

        echo $view->render($name ?? $this->method, $variables);
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