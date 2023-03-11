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
        return '\app\controller\\' . $controllerName;
    }

    /**
     * Generate admin panel controller class name
     * @param string $controllerName
     * @return string
     */
    public static function controllerPa(string $controllerName): string
    {
        return '\Krzysztofzylka\MicroFramework\AdminPanel\controller\\' . $controllerName;
    }

    /**
     * Generate local admin panel controller class name
     * @param string $controllerName
     * @return string
     */
    public static function controllerPaLocal(string $controllerName): string
    {
        return '\admin_panel\controller\\' . $controllerName;
    }

    /**
     * Generate api controller class name
     * @param string $controllerName
     * @return string
     */
    public static function controllerApi(string $controllerName): string
    {
        return '\api\controller\\' . $controllerName;
    }

    /**
     * Generate model class name
     * @param string $modelName
     * @return string
     */
    public static function model(string $modelName): string
    {
        return '\app\model\\' . $modelName;
    }

    /**
     * Generate model class name
     * @param string $modelName
     * @return string
     */
    public static function modelPa(string $modelName): string
    {
        $modelName = lcfirst(substr($modelName, 2));

        return '\Krzysztofzylka\MicroFramework\AdminPanel\model\\' . $modelName;
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