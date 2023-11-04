<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;

/**
 * Class Controller
 * @package Krzysztofzylka\MicroFramework
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

    public function loadModel(string ...$model): Model
    {
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
        DebugBar::addMessage($modelClass, 'Model object');
        return $modelClass;
    }

}