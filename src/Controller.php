<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;

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
     * Load models
     * @param string ...$model
     * @return Model
     * @throws NotFoundException
     * @throws Exception
     */
    public function loadModel(string ...$model): Model
    {
        DebugBar::addMessage('Load model\'s ' . implode(', ', $model), 'Load model');

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

        DebugBar::timeStop('load_model');
        DebugBar::addModelMessage($modelClass);
        return $modelClass;
    }

    /**
     * Load view
     * @param string|null $action
     * @return bool
     * @throws NotFoundException
     */
    public function loadView(?string $action = null): bool
    {
        DebugBar::timeStart('view', 'Load view');
        $action = $action ?? ($this->name . '/' . $this->action);

        /** @var View $view */
        $view = new $_ENV['CLASS_VIEW']();
        $view->controller = $this;
        $view->variables = $this->viewVariables;
        $view->action = $action;
        $view->render();

        DebugBar::timeStop('view');
        DebugBar::addMessage($view, 'Load view');

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

}