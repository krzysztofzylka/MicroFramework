<?php

namespace Krzysztofzylka\MicroFramework\Extra;

class ObjectNameGenerator {

    /**
     * Generate controller class name
     * @param string $controllerName
     * @return string
     */
    public static function controller(string $controllerName) : string {
        return '\controller\\' . $controllerName;
    }

    /**
     * Generate api controller class name
     * @param string $controllerName
     * @return string
     */
    public static function controllerApi(string $controllerName) : string {
        return '\api_controller\\' . $controllerName;
    }

    /**
     * Generate model class name
     * @param string $modelName
     * @return string
     */
    public static function model(string $modelName) : string {
        return '\model\\' . $modelName;
    }

}