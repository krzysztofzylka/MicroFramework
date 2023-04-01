<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation\Predefined;

use Krzysztofzylka\MicroFramework\Exception\ValidationException;

/**
 * Length checker
 * @package Validation
 */
class length
{

    /**
     * @param $value
     * @param $key
     * @param $data
     * @throws ValidationException
     */
    public function __construct($value, $key, $data)
    {
        if (isset($data['min']) && strlen($value) < $data['min']) {
            throw new ValidationException(__('micro-framework.validation.predefined.length.min') . ' ' . $data['min']);
        } elseif (isset($data['max']) && strlen($value) > $data['max']) {
            throw new ValidationException(__('micro-framework.validation.predefined.length.max') . ' ' . $data['max']);
        }
    }

}