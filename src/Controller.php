<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;

class Controller {

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
     * Load model
     * @param string $name
     * @return Model
     * @throws NotFoundException
     */
    public function loadModel(string $name) : Model {
        $class = ObjectNameGenerator::model($name);

        try {
            /** @var Model $model */
            $model = new $class();
            $model->name = $name;
            $model->controller = $this;
        } catch (\Exception) {
            throw new NotFoundException();
        }

        $this->models[ucfirst($name)] = $model;

        return $model;
    }

    /**
     * Load view
     * @param string $name
     * @param array $variables
     * @return void
     * @throws Exception\ViewException
     */
    public function loadView(string $name, array $variables = []) : void {
        $view = new View();

        echo $view->render($name, $variables);
    }

    /**
     * Magic __get
     * @param string $name
     * @return mixed|Model
     */
    public function __get(string $name) : mixed {
        if (in_array($name, array_keys($this->models))) {
            return $this->models[$name];
        }

        return trigger_error('Undefined property ' . $name, E_USER_WARNING);
    }

}