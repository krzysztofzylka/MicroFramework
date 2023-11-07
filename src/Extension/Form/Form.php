<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form;

use Exception;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Model;

/**
 * Główna klasa kontrolera

 */
class Form
{

    /**
     * Controller instance
     * @var Controller
     */
    protected Controller $controller;

    /**
     * Model instance
     * @var Model
     */
    protected Model $model;

    /**
     * Generated form
     * @var string
     */
    protected string $formHtml = '';

    /**
     * Validations
     * @var array
     */
    public array $validation = [];

    /**
     * Data
     * @var array
     */
    protected array $data = [];

    /**
     * Constructor
     * @param Controller $controller controller instance
     * @param Model $model
     * @throws Exception
     */
    public function __construct(Controller $controller, Model $model)
    {
        $this->controller = $controller;
        $this->model = $model;
        $this->validation = $this->loadValidation();
    }

    /**
     * Return form
     * @return string
     */
    public function __toString(): string
    {
        DebugBar::addFormMessage($this, 'Generate form');

        return $this->formHtml;
    }

    /**
     * Success
     * @return bool
     */
    public function success(): bool
    {
        if (!$this->controller->data || empty($this->controller->data) || !empty($this->validation)) {
            return false;
        }

        return true;
    }

    /**
     * Generate form name
     * @param string $name
     * @param string $prefix
     * @return string
     */
    protected function generateName(string $name, string $prefix = ''): string
    {
        $core = str_starts_with($name, '/');

        if ($core) {
            $name = substr($name, 1);
            $prefix .= '/';
        }

        $explode = explode('/', $name, 2);

        return $prefix . $explode[0] . (isset($explode[1]) ? ('[' . implode('][', explode('/', $explode[1])) . ']') : '');
    }

    /**
     * Generate form id
     * @param string $name
     * @return string
     */
    protected function generateId(string $name): string
    {
        $return = '';
        $explode = explode('/', $name);

        foreach ($explode as $value) {
            $value = mb_strtolower($value);
            $return .= empty($return) ? $value : ucfirst($value);
        }

        return $return;
    }

    /**
     * Load validation
     * @return array
     * @throws Exception
     */
    protected function loadValidation(): array
    {
        if (!method_exists($this->model, 'formValidation') || !$this->controller->data) {
            return [];
        }

        DebugBar::timeStart('validation', 'Form validation');

        $validations = [];

        foreach ($this->model->formValidation() as $name => $data) {
            foreach ($data as $validationData) {
                if (is_object($validationData)) {
                    try {
                        $validationData($this->getData($name));
                    } catch (Exception $exception) {
                        $validations[$name] = $exception->getMessage();
                        $this->model->validationErrors[$name] = $exception->getMessage();

                        continue 2;
                    }
                }
            }
        }

        DebugBar::timeStop('validation');

        if (!empty($validations)) {
            Log::log('Validation errors', 'ERROR', $validations);
            DebugBar::addFormMessage($validations, 'Validation errors');
        }

        return $validations;
    }

    /**
     * Get data
     * @param string $name
     * @return ?string
     */
    protected function getData(string $name): mixed
    {
        $generatedArray = '["' . implode('"]["', explode('/', $name)) . '"]';

        if ($generatedArray === '[""]') {
            return null;
        }

        $generatedArray = str_replace('[""]', '', $generatedArray);
        return @eval('return $this->controller->data' . $generatedArray . ';');
    }

}