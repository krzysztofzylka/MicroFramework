<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form;

class InternalFormGenerator
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

}