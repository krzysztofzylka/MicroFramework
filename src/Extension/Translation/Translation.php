<?php

namespace Krzysztofzylka\MicroFramework\Extension\Translation;

use krzysztofzylka\SimpleLibraries\Library\_Array;
use Symfony\Component\Yaml\Yaml;

/**
 * Translation
 * @package Extension\Translation
 */
class Translation
{

    static public array $translation = [];

    /**
     * Read translation file
     * @param string $path
     * @return void
     */
    public static function getTranslationFile(string $path): void
    {
        self::$translation = array_merge(self::$translation, Yaml::parse(file_get_contents($path)));
    }

    /**
     * Get translation
     * @param string $name
     * @return mixed
     */
    public static function get(string $name): mixed
    {
        return _Array::getFromArrayUsingString($name, self::$translation);
    }

}