<?php

namespace Krzysztofzylka\MicroFramework\bin\Console;

use config\Config;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use Krzysztofzylka\MicroFramework\Kernel;
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
     * Disable die in print methods
     * @var bool
     */
    public static bool $disableDiePrint = false;

    /**
     * Resources path
     * @var string
     */
    public string $resourcesPath;

    /**
     * Cron path
     * @var string
     */
    public string $cronPath;

    public function __construct(array $argv)
    {
        $this->getArguments($argv);

        $class = '\Krzysztofzylka\MicroFramework\bin\Action\\' . Strings::camelizeString($this->action, '_');
        $this->cronPath = $this->path . '/config/Cron.php';

        if (!file_exists($this->cronPath)) {
            $this->cronPath = false;
        }

        try {
            if (!class_exists($class)) {
                $this->dprint('Action not exists.');
            }
        } catch (\Exception) {
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

        if (!isset($argv[1])) {
            $argv[1] = 'help';
        }

        $this->action = $argv[1];

        $this->resourcesPath = realpath(__DIR__ . '/../resources/');

        Kernel::initPaths($this->path);
        Kernel::autoload();
        Kernel::setConfig(new Config());
        Kernel::configDatabaseConnect();
    }

}