<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form\Helper;

use Krzysztofzylka\MicroFramework\Kernel;

/**
 * Form helper
 * @package Extension\Form\Helper
 */
class FormHelper
{

    /**
     * Generate form name
     * @param string $name
     * @param string $prefix
     * @return string
     */
    public static function generateName(string $name, string $prefix = ''): string
    {
        $core = str_starts_with($name, '/');

        if ($core) {
            $name = substr($name, 1);
            $prefix .= '/';
        }

        $explode = explode('/', $name, 2);

        return $prefix . $explode[0] . (isset($explode[1]) ? ('[' . implode('][', explode('/', $explode[1])) . ']') : '');
    }

    /**
     * Generate form id
     * @param string $name
     * @return string
     */
    public static function generateId(string $name): string
    {
        $return = '';
        $explode = explode('/', $name);

        foreach ($explode as $value) {
            $value = mb_strtolower($value);
            $return .= empty($return) ? $value : ucfirst($value);
        }

        return $return;
    }

    /**
     * Get data
     * @param string $name
     * @param ?array $params
     * @param array $attributes
     * @param array|null $postData
     * @return ?string
     * @ignore
     */
    public static function getData(
        string $name,
        ?array &$params = null,
        array &$attributes = [],
        ?array $postData = null
    ): mixed
    {
        $generatedArray = '["' . implode('"]["', explode('/', $name)) . '"]';

        if ($generatedArray === '[""]') {
            return null;
        }

        $data = $postData ?? Kernel::getData();
        $generatedArray = str_replace('[""]', '', $generatedArray);
        $dataString = @eval('return $data' . $generatedArray . ';');

        if ($dataString && !is_null($params)) {
            unset($attributes['value']);

            $params = [...$params, 'value' => $dataString];
        }

        return $dataString;
    }

}