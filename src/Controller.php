<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Extension\Response;
use krzysztofzylka\SimpleLibraries\Library\Strings;

/**
 * Class Controller
 */
class Controller
{

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
     * Loaded models
     * @var array
     */
    public array $models = [];

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
     * Load models
     * @param string ...$model
     * @return Model
     * @throws NotFoundException
     * @throws Exception
     */
    public function loadModel(string ...$model): Model
    {
        DebugBar::addFrameworkMessage('Load model\'s ' . implode(', ', $model), 'Load model');

        $model = count($model) > 1 ? $model : $model[0];

        if (is_array($model)) {
            DebugBar::timeStart('load_model_group', 'Load models ' . implode(', ', $model));

            foreach ($model as $m) {
                $modelClass = $this->loadModel($m);
            }

            DebugBar::timeStop('load_model_group');

            return $modelClass;
        }

        DebugBar::timeStart('load_model', 'Load model ' . $model);
        $className = 'src\Model\\' . $model;

        if (!class_exists($className)) {
            Log::log('Model ' . $model . ' not found', 'ERR');

            throw new NotFoundException('Model ' . $model . ' not found');
        }

        /** @var Model $modelClass */
        $modelClass = new $className();
        $modelClass->name = $model;
        $modelClass->controller = $this;
        $modelClass->transactionInstance = new Transaction();

        if ($_ENV['DATABASE'] && $modelClass->useTable !== false) {
            $modelClass->useTable = $modelClass->useTable ?? $modelClass->name;
            $modelClass->tableInstance = new Table($modelClass->useTable);
        }

        $this->models[Strings::camelizeString($model, '_')] = $modelClass;
        DebugBar::timeStop('load_model');
        DebugBar::addModelMessage($modelClass);
        return $modelClass;
    }

    /**
     * Load view
     * @param string|null $action
     * @return bool
     * @throws NotFoundException
     * @throws MicroFrameworkException
     * @throws Exception
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
     * Set view variable
     * @param $name
     * @param $value
     * @return void
     */
    public function set($name, $value): void
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

}