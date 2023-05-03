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
            $explode = explode('=', $content, 2);
            $_ENV[strtolower($this->fileName) . '.' . $explode[0]] = $explode[1];
        }

        ksort($_ENV);
    }

}