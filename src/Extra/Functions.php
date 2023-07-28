<?php

use Krzysztofzylka\MicroFramework\Extension\Translation\Translation;

/**
 * Better var_dump
 * @param $data
 * @return void
 */
function dump(...$data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * Better var_dump with exit
 * @param $data
 * @return void
 */
function dumpe(...$data): void
{
    dump(...$data);
    exit;
}

/**
 * Translation
 * @param string $name
 * @param array $variables
 * @return mixed
 */
function __(string $name, array $variables = []): mixed
{
    $translation = Translation::get($name) ?? '{' . $name . '}';

    foreach ($variables as $key => $variable) {
        $translation = str_replace('{' . $key . '}', $variable, $translation);
    }

    return $translation;
}