<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation\Predefined;

use Krzysztofzylka\MicroFramework\Exception\ValidationException;

/**
 * Check e-mail
 * @package Validation
 */
class isEmail {

    /**
     * @param $value
     * @param $key
     * @param $data
     * @throws ValidationException
     */
    public function __construct($value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email format');
        }
    }

}