<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form\Trait;

use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;

trait Validation {

    /**
     * Get validation error
     * @param $name
     * @return false|string
     */
    private function getValidation($name): false|string
    {
        foreach ($this->controller->models as $model) {
            if (!empty($model->validationErrors)) {
                $validation = $model->validationErrors;

                foreach (explode('/', $name) as $explodeName) {
                    if (!isset($validation[$explodeName])) {
                        return false;
                    }

                    $validation = $validation[$explodeName];
                }
            }
        }

        return $validation ?? false;
    }

    /**
     * Generate invalid feedback tag
     * @param string|false $invalidText
     * @return string
     * @throws MicroFrameworkException
     */
    private function generateInvalidDiv(string|false $invalidText): string
    {
        if ($invalidText) {
            return (new Html())->clearTag('div', $invalidText, ['id' => 'fieldError', 'class' => 'invalid-feedback'])->__toString();
        }

        return '';
    }

}