<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use Exception;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Debug;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;
use Krzysztofzylka\MicroFramework\Model as ModelClass;
use krzysztofzylka\SimpleLibraries\Library\Strings;

/**
 * Model
 * @package Trait
 */
trait Model
{

    /**
     * Models
     * @var array
     */
    public array $models = [];

    /**
     * Parent model
     * @var ?ModelClass
     */
    public ?ModelClass $parentModel = null;

    /**
     * Load model
     * @param string ...$name
     * @return ModelClass
     * @throws NotFoundException
     */
    public function loadModel(string ...$name): ModelClass
    {
        if ($_ENV['config_debug']) {
            $time_start = microtime(true);
        }

        if (count($name) > 1) {
            foreach ($name as $singleName) {
                $lastModel = $this->loadModel($singleName);
            }

            if ($_ENV['config_debug']) {
                Debug::$data['times']['model_' . $singleName . '_' . random_int(0, 99999)] = microtime(true) - $time_start;
                Debug::$data['models'][$singleName] = (Debug::$data['models'][$singleName] ?? 0) + 1;
            }

            return $lastModel;
        } else {
            $name = $name[0];
        }

        $startName = $name;
        $params = isset($this->params) ? $this->params : $this->controller->params;

        if (isset($params['admin_panel']) && $params['admin_panel']) {
            $class = ObjectNameGenerator::modelPaLocal($name);

            if (!class_exists($class)) {
                $class = ObjectNameGenerator::modelPa($name);
            }

            if (!class_exists($class)) {
                $class = ObjectNameGenerator::model($name);
            }
        } else {
            $class = ObjectNameGenerator::model($name);
        }

        if (!class_exists($class)) {
            throw new NotFoundException(__('micro-framework.model.not_found', ['name' => $startName]));
        }

        try {
            /** @var ModelClass $model */
            $model = new $class();
            $model->name = $name;
            $model->data = $this->data;

            if ($this instanceof ModelClass) {
                $this->parentModel = $model;
                $model->controller = $this->controller;
            } elseif ($this instanceof Controller) {
                $model->controller = $this;
            }

            if ($model->useTable && isset(DatabaseManager::$connection)) {
                $model->tableInstance = new Table($model->tableName ?? $startName);
                $model->transactionInstance = new Transaction();
            }
        } catch (Exception $exception) {
            $this->log(__('micro-framework.model.fail_load', ['name' => $name]), 'ERR', ['name' => $startName, 'class' => $class, 'exception' => $exception]);

            throw new NotFoundException(__('micro-framework.model.not_found', ['name' => $startName]));
        }

        $this->models[Strings::camelizeString(str_replace('\\', '_', $startName), '_')] = $model;

        if ($_ENV['config_debug']) {
            Debug::$data['times']['model_' . $name . '_' . random_int(0, 99999)] = microtime(true) - $time_start;
            Debug::$data['models'][$name] = (Debug::$data['models'][$name] ?? 0) + 1;
        }

        return $model;
    }

}