<?php

namespace Krzysztofzylka\MicroFramework\bin\Console;

use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use krzysztofzylka\SimpleLibraries\Library\Strings;

class Console
{

    use Prints;

    /**
     * Arguments
     * @var array
     */
    public array $arg;

    /**
     * Action
     * @var string
     */
    public string $action;

    /**
     * Path
     * @var string
     */
    public string $path;

    /**
     * Resources path
     * @var string
     */
    public string $resourcesPath;

    public function __construct(array $argv)
    {
        $this->getArguments($argv);

        $class = '\Krzysztofzylka\MicroFramework\bin\Action\\' . Strings::camelizeString($this->action, '_');

        if (!class_exists($class)) {
            $this->dprint('Action not exists.');
        }

        new $class($this);
    }

    /**
     * Get arguments
     * @param array $argv
     * @return void
     */
    private function getArguments(array $argv): void
    {
        $this->path = getcwd();
        $this->arg = $argv;
        $this->action = $argv[1];
        $this->resourcesPath = realpath(__DIR__ . '/../resources/');
    }

}