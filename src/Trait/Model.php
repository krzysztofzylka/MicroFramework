<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use Exception;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\MicroFramework\Controller;
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
        if (count($name) > 1) {
            foreach ($name as $singleName) {
                $lastModel = $this->loadModel($singleName);
            }

            return $lastModel;
        } else {
            $name = $name[0];
        }

        $startName = $name;
        $params = $this->params ?? $this->controller->params;

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
            throw new NotFoundException('Not found model ' . $startName);
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
            $this->log('Fail load model ' . $name, 'ERR', ['name' => $startName, 'class' => $class, 'exception' => $exception]);

            throw new NotFoundException('Not found model ' . $startName);
        }

        $this->models[Strings::camelizeString($startName, '_')] = $model;

        return $model;
    }

}