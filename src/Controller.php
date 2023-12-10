<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\Strings\Strings;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Extension\Response;

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
     * Loads a model or a group of models.
     * @param string ...$model The name(s) of the model(s) to load.
     * @return Model The loaded model instance.
     * @throws NotFoundException If the specified model is not found.
     */
    public function loadModel(string ...$model): Model
    {
        DebugBar::addFrameworkMessage('Load model\'s ' . implode(', ', $model), 'Load model');
        $debugHash = Generator::uniqHash();

        $model = count($model) > 1 ? $model : $model[0];

        if (is_array($model)) {
            DebugBar::timeStart('load_model_group' . $debugHash, 'Load models ' . implode(', ', $model));

            foreach ($model as $m) {
                $modelClass = $this->loadModel($m);
            }

            DebugBar::timeStop('load_model_group' . $debugHash);

            return $modelClass;
        }

        DebugBar::timeStart('load_model' . $debugHash, 'Load model ' . $model);
        $className = 'src\Model\\' . $model;

        if (!class_exists($className)) {
            Log::log('Model ' . $model . ' not found', 'ERR');

            throw new NotFoundException('Model ' . $model . ' not found');
        }

        /** @var Model $modelClass */
        $modelClass = new $className();
        $modelClass->name = $model;
        $modelClass->controller = $this;

        if ($_ENV['DATABASE'] && $modelClass->useTable !== false) {
            $modelClass->transactionInstance = new Transaction();
            $modelClass->useTable = $modelClass->useTable ?? $modelClass->name;
            $modelClass->tableInstance = new Table($modelClass->useTable);
        }

        $this->models[Strings::camelizeString($model, '_')] = $modelClass;
        DebugBar::timeStop('load_model' . $debugHash);
        DebugBar::addModelMessage($modelClass);

        return $modelClass;
    }

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

}