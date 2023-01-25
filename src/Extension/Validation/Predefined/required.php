<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation\Predefined;

use Krzysztofzylka\MicroFramework\Exception\ValidationException;

/**
 * Required content
 * @package Validation
 */
class required {

    /**
     * @param $value
     * @throws ValidationException
     */
    public function __construct($value) {
        if (empty($value)) {
            throw new ValidationException('This field is required');
        }
    }

}