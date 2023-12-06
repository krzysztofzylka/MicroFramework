<?php

namespace Krzysztofzylka\MicroFramework\Extension\Env;

/**
 * Env instance
 * @package Extension\Env
 */
class Env
{

    /**
     * Env filepath
     * @var string
     */
    private string $filePath;

    /**
     * Initialize ENV file
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->filePath = realpath($path);
    }

    /**
     * Load ENV File
     * @return void
     */
    public function load(): void
    {
        if (!$this->filePath || !file_exists($this->filePath)) {
            return;
        }

        $contents = file_get_contents($this->filePath);

        if ($contents === false) {
            return;
        }

        $contents = explode(PHP_EOL, $contents);

        foreach ($contents as $content) {
            $content = ltrim($content);

            if (str_starts_with($content, '#') || empty($content)) {
                continue;
            }

            $explode = explode('=', $content, 2);

            if (count($explode) < 2) {
                continue;
            }

            $name = $explode[0];
            $value = $explode[1];

            if (str_starts_with($value, '"') && str_ends_with($value, '"') || str_starts_with($value, "'") && str_ends_with($value, "'")) {
                $value = substr($value, 1, -1);
            } else {
                if (preg_match("/^\d+$/", $value)) {
                    $value = (int)$value;
                } elseif (preg_match("/^\d+\.\d+$/", $value)) {
                    $value = (float)$value;
                }

                switch (strtolower($value)) {
                    case 'false':
                        $value = false;
                        break;
                    case 'true':
                        $value = true;
                        break;
                    case 'null':
                        $value = null;
                        break;
                }
            }

            $_ENV[strtoupper($name)] = $value;
        }

        ksort($_ENV);
    }

}