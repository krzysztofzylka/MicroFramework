<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use Krzysztofzylka\MicroFramework\Extension\Validation\Validation;
use Krzysztofzylka\MicroFramework\Kernel;

trait ModelValidation {

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
     * @return bool
     */
    public function validate(?array $data = null): bool
    {
        $validationData = $this->loadValidation();

        if (is_null($validationData)) {
            return true;
        }

        $validation = new Validation();
        $validation->setValidation($validationData);
        $this->validationErrors = $validation->validate($data ?? $this->data);

        if (!empty($this->validationErrors) && Kernel::getConfig()->debug) {
            $this->log('Validation fail', 'WARNING', $this->validationErrors);
        }

        return empty($this->validationErrors);
    }

}