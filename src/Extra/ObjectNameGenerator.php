<?php

namespace Krzysztofzylka\MicroFramework\Extra;

/**
 * Object name generator
 * @package Extra
 */
class ObjectNameGenerator
{

    /**
     * Generate controller class name
     * @param string $controllerName
     * @return string
     */
    public static function controller(string $controllerName): string
    {
        return '\controller\\' . $controllerName;
    }

    /**
     * Generate api controller class name
     * @param string $controllerName
     * @return string
     */
    public static function controllerApi(string $controllerName): string
    {
        return '\api_controller\\' . $controllerName;
    }

    /**
     * Generate model class name
     * @param string $modelName
     * @return string
     */
    public static function model(string $modelName): string
    {
        return '\model\\' . $modelName;
    }

    /**
     * Generate predefined validation class name
     * @param string $validationName
     * @return string
     */
    public static function predefinedValidationClass(string $validationName): string
    {
        return '\Krzysztofzylka\MicroFramework\Extension\Validation\Predefined\\' . $validationName;
    }

}