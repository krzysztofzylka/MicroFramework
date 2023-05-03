<?php

namespace Krzysztofzylka\MicroFramework\Extension\Env;

use Krzysztofzylka\MicroFramework\Kernel;

/**
 * Env instance
 * @package Extension\Env
 */
class EnvInstance
{

    /**
     * Env filename
     * @var string
     */
    private string $fileName;

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
        $this->fileName = explode('.', strtolower(basename($path)), 2)[0];
    }

    /**
     * Load ENV File
     * @return void
     */
    public function load(): void
    {
        if (!$this->filePath) {
            return;
        }

        $contents = file_get_contents($this->filePath);
        $contents = explode(PHP_EOL, $contents);

        foreach($contents as $content) {
            $content = ltrim($content);

            if (str_starts_with($content, '#') || empty($content)) {
                continue;
            }

            $explode = explode('=', $content, 2);
            $value = $explode[1];

            if (str_starts_with($value, '"') && str_ends_with($value, '"') || str_starts_with($value, "'") && str_ends_with($value, "'")) {
                $value = (string)substr($value, 1, -1);
            } else {
                if (intval($value) && substr_count($value, '.') === 0) {
                    $value = (int)$value;
                } elseif (floatval($value) && substr_count($value, '.') === 1) {
                    $value = (float)$value;
                }

                switch ($value) {
                    case 'false':
                        $value = false;
                        break;
                    case 'true':
                        $value = true;
                        break;
                    case 'NULL':
                        $value = null;
                        break;
                }
            }

            $_ENV[strtolower($this->fileName) . '.' . $explode[0]] = $value;
        }

        ksort($_ENV);
    }

}