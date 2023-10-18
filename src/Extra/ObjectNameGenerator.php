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
     * @param ObjectTypeEnum $objectTypeEnum Enum
     * @return string
     */
    public static function controller(string $controllerName, ObjectTypeEnum $objectTypeEnum): string
    {
        $objectName = match($objectTypeEnum) {
            ObjectTypeEnum::APP_LOCAL => '\app\controller\\',
            ObjectTypeEnum::APP => '\Krzysztofzylka\MicroFramework\App\controller\\',
            ObjectTypeEnum::PA => '\Krzysztofzylka\MicroFramework\AdminPanel\controller\\',
            ObjectTypeEnum::PA_LOCAL => '\admin_panel\controller\\',
            ObjectTypeEnum::API => '\api\controller\\'
        };

        if (!class_exists($objectName . $controllerName)) {
            $objectNameDirectory = $objectName . explode('_', $controllerName)[0] . '\\';

            if (class_exists($objectNameDirectory . $controllerName)) {
                return $objectNameDirectory . $controllerName;
            }
        }

        return $objectName . $controllerName;
    }

    /**
     * Generate model class name
     * @param string $modelName
     * @param ObjectTypeEnum $objectTypeEnum
     * @return string
     */
    public static function model(string $modelName, ObjectTypeEnum $objectTypeEnum = ObjectTypeEnum::APP): string
    {
        $objectName = match($objectTypeEnum) {
            ObjectTypeEnum::APP => '\app\model\\',
            ObjectTypeEnum::PA => '\Krzysztofzylka\MicroFramework\AdminPanel\model\\',
            ObjectTypeEnum::PA_LOCAL => '\admin_panel\model\\'
        };

        if (!class_exists($objectName . $modelName)) {
            $objectNameDirectory = $objectName . explode('_', $modelName)[0] . '\\';

            if (class_exists($objectNameDirectory . $modelName)) {
                return $objectNameDirectory . $modelName;
            }
        }

        return $objectName . $modelName;
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