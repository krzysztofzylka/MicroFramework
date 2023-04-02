<?php

namespace Krzysztofzylka\MicroFramework\Extension\Translation;

use krzysztofzylka\SimpleLibraries\Library\_Array;

/**
 * Translation
 * @package Extension\Translation
 */
class Translation {

    static private array $translation = [];

    /**
     * Read translation file
     * @param string $path
     * @return void
     */
    public static function getTranslationFile(string $path): void
    {
        self::$translation = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($path));
    }

    /**
     * Get translation
     * @param string $name
     * @return mixed
     */
    public static function get(string $name) : mixed {
        return _Array::getFromArrayUsingString($name, self::$translation);
    }

}