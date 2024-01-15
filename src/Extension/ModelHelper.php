<?php

namespace Krzysztofzylka\MicroFramework\Extension;

use Exception;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Model;
use Krzysztofzylka\Strings\Strings;

trait ModelHelper
{


    /**
     * Loaded models
     * @var array
     */
    public array $models = [];

    /**
     * Loads a model or a group of models.
     * @param string ...$model The name(s) of the model(s) to load.
     * @return Model The loaded model instance.
     * @throws NotFoundException If the specified model is not found.
     * @throws Exception
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

        if ($this instanceof Controller) {
            $modelClass->controller = $this;
        } elseif (isset($this->controller)) {
            $modelClass->controller = $this->controller;
        } else {
            $modelClass->controller = new Controller();
        }

        if ($_ENV['DATABASE'] && $modelClass->useTable !== false) {
            $modelClass->transactionInstance = new Transaction();
            $modelClass->useTable = $modelClass->useTable ?? $modelClass->name;
            $modelClass->tableInstance = new Table($modelClass->useTable);

            if ($this instanceof Model && $this->isolatorName === $modelClass->isolatorName) {
                $modelClass->isolator = $this->isolator;
            }
        }

        $this->models[Strings::camelizeString($model, '_')] = $modelClass;
        DebugBar::timeStop('load_model' . $debugHash);
        DebugBar::addModelMessage($modelClass);

        return $modelClass;
    }

}