<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation\Predefined;

use Krzysztofzylka\MicroFramework\Exception\ValidationException;

/**
 * Check date
 * @package Validation
 */
class isValidDate {

    /**
     * @param $value
     * @throws ValidationException
     */
    public function __construct($value) {
        if (!empty($value) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value)) {
            throw new ValidationException('Invalid date format');
        }
    }

}