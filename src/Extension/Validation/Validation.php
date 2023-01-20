<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation;

class Validation {

    private array $validation;

    /**
     * Set array validation
     * @param array $validation
     */
    public function setValidation(array $validation) : void {
        $this->validation = $validation;
    }

    /**
     * Valiate data
     * @param array $data
     * @return array
     */
    public function validate(array $data) : array {
        foreach ($data as $model => $modelData) {

        }
    }


}