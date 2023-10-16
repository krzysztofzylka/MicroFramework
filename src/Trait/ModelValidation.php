<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use Krzysztofzylka\MicroFramework\Extension\Validation\Validation;
use krzysztofzylka\SimpleLibraries\Library\Request;
use krzysztofzylka\SimpleLibraries\Library\Response;

trait ModelValidation
{

    /**
     * Validation errors
     * @var ?array
     */
    public ?array $validationErrors = [];

    /**
     * Validation array
     * @return array
     */
    public function loadValidation()
    {
    }

    /**
     * Validate form
     * @param ?array $data
     * @param bool $responseAjax
     * @return bool
     */
    public function validate(?array $data = null, bool $responseAjax = true): bool
    {
        $validationData = $this->loadValidation();

        if (is_null($validationData)) {
            return true;
        }

        $validation = new Validation();
        $validation->setValidation($validationData);
        $this->validationErrors = $validation->validate($data ?? $this->data);

        if (!empty($this->validationErrors) && $_ENV['config_debug']) {
            $this->log(__('micro-framework.validation.fail'), 'WARNING', $this->validationErrors);
        }

        if (Request::isAjaxRequest() && !empty($this->validationErrors)) {
            $errorList = [];

            foreach ($this->validationErrors as $validationKey => $validationErrors) {
                foreach ($validationErrors as $validationErrorKey => $validationError) {
                    $errorList[$validationKey . "[$validationErrorKey]"] = $validationError;
                }
            }

            $response = new Response();
            $response->json([
                'type' => 'formValidatorErrorResponse',
                'list' => $errorList
            ]);
        }

        return empty($this->validationErrors);
    }

}