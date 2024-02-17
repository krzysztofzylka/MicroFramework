<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form;

use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\Request\Request;
use Exception;

/**
 * Form component
 */
class Form
{

    use InternalFormElements;

    /**
     * Validations
     * @var array
     */
    public array $validation = [];

    /**
     * Form id
     * @var string|null
     */
    private ?string $id = null;

    /**
     * Controller instance
     * @var Controller
     */
    protected Controller $controller;

    /**
     * Generated form
     * @var string
     */
    protected string $formHtml = '';

    /**
     * Data
     * @var array
     */
    protected array $data = [];

    /**
     * Validation data
     * @var ?array
     */
    protected ?array $validationData = null;

    /**
     * Constructor
     * @param Controller $controller controller instance
     * @param array|null $validations
     * @param array|null $validationData
     * @throws Exception
     */
    public function __construct(Controller $controller, ?array $validations = null, ?array $validationData = null)
    {
        $this->controller = $controller;
        $this->setValidationData($validationData);
        $this->loadValidation($validations);
    }

    /**
     * Set validation data
     * @param ?array $validationData
     * @return self
     */
    public function setValidationData(?array $validationData): self
    {
        $this->validationData = $validationData;

        return $this;
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
     * Set id
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
        $this->hiddenInput('formId', $id);
    }

    /**
     * Submit form
     * @return bool
     */
    public function onSubmit(): bool
    {
        if (!Request::isPost()
            || !$this->controller->data
            || empty($this->controller->data)
            || !empty($this->validation)
        ) {
            return false;
        }

        if (!is_null($this->id)) {
            return $this->controller->data['formId'] === $this->id;
        }

        return true;
    }

    /**
     * Retrieves the value of a data property based on the provided name.
     * @param string $name The name of the data property.
     * @return mixed|null The value of the data property. Returns null if the name is empty or the data property does not exist.
     */
    protected function getData(string $name): mixed
    {
        /**
         * Represents an array that contains generated values.
         * @var array $generatedArray
         */
        $generatedArray = '["' . implode('"]["', explode('/', $name)) . '"]';

        if ($generatedArray === '[""]') {
            return null;
        }

        $generatedArray = str_replace('[""]', '', $generatedArray);
        return @eval('return $this->controller->data' . $generatedArray . ';');
    }

    /**
     * Load validation
     * @param array|null $validations custom validations
     * @return void
     * @throws Exception
     */
    protected function loadValidation(?array $validations = null): void
    {
        $data = $this->controller->data ?? $this->validationData;

        if (!$data) {
            return;
        }

        DebugBar::timeStart('validation', 'Form validation');

        foreach ($validations ?? [] as $name => $data) {
            foreach ($data as $validationData) {
                if (is_object($validationData)) {
                    try {
                        $validationData($this->getData($name), $this->validation);
                    } catch (Exception $exception) {
                        $this->validation[$name] = $exception->getMessage();

                        continue 2;
                    }
                }
            }
        }

        DebugBar::timeStop('validation');

        if (!empty($this->validation)) {
            Log::log('Validation errors', 'ERROR', $this->validation);
            DebugBar::addFormMessage($this->validation, 'Validation errors');
        }
    }

}