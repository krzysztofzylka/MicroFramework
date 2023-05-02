<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation\Predefined;

use Krzysztofzylka\MicroFramework\Exception\ValidationException;

/**
 * Check e-mail
 * @package Extension\Validation\Predefined
 */
class isNumeric
{

    /**
     * @param $value
     * @throws ValidationException
     */
    public function __construct($value)
    {
        if (!is_numeric($value)) {
            throw new ValidationException(__('micro-framework.validation.predefined.is_numeric'));
        }
    }

}