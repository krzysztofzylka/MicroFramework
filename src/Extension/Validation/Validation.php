<?php

namespace Krzysztofzylka\MicroFramework\Extension\Validation;

use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Exception\ValidationException;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;

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
    public function validate(?array $data) : array {
        $errors = [];

        foreach ($data as $model => $modelData) {
            if (!isset($this->validation[$model])) {
                continue;
            }

            $modelValidation = $this->validation[$model];

            foreach ($modelData as $name => $value) {
                if (!isset($modelValidation[$name])) {
                    continue;
                }

                $scanErrors = $this->_validateElement($name, $value, $modelValidation[$name]);

                if (is_string($scanErrors)) {
                    $errors[$model][$name] = $scanErrors;
                }
            }
        }

        return $errors;
    }

    /**
     * Validate element
     * @param string $name
     * @param $value
     * @param array $elementValidations
     * @return ?string
     */
    private function _validateElement(string $name, $value, array $elementValidations) : ?string {
        foreach ($elementValidations as $elementValidationKey => $elementValidation) {
            if (is_object($elementValidation)) {
                try {
                    $elementValidation($value);
                } catch (ValidationException $validationException) {
                    return $validationException->getMessage();
                }
            } elseif (is_int($elementValidationKey)) {
                try {
                    $object = ObjectNameGenerator::predefinedValidationClass($elementValidation);

                    new $object($value, $elementValidationKey, $elementValidation);
                } catch (NotFoundException) {
                    continue;
                } catch (ValidationException $validationException) {
                    return $validationException->getMessage();
                }
            } else {
                try {
                    $object = ObjectNameGenerator::predefinedValidationClass($elementValidationKey);

                    new $object($value, $elementValidationKey, $elementValidation);
                } catch (NotFoundException) {
                    continue;
                } catch (ValidationException $validationException) {
                    return $validationException->getMessage();
                }
            }
        }

        return null;
    }

}