<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation\Predefined;

use Krzysztofzylka\MicroFramework\Exception\ValidationException;

/**
 * Allow only values from array
 * @package Extension\Validation\Predefined
 */
class allowValues
{

    /**
     * @param $value
     * @throws ValidationException
     */
    public function __construct($value, $key, $data)
    {
        if (!in_array($value, $data)) {
            throw new ValidationException(__('micro-framework.validation.predefined.allow_values', ['valueList' => implode(', ', $data)]));
        }
    }

}