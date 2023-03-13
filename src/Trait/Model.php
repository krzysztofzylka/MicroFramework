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

        if (isset($this->params['admin_panel']) && $this->params['admin_panel']) {
            $class = ObjectNameGenerator::modelPaLocal($name);

            if (!class_exists($class)) {
                $class = ObjectNameGenerator::modelPa($name);
            }
        } else {
            $class = ObjectNameGenerator::model($name);
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

        $this->models[str_replace('_', '', ucwords($startName, '_'))] = $model;

        return $model;
    }

}