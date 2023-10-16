<?php

use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Console\Args;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;
use krzysztofzylka\SimpleLibraries\Library\Strings;

if (file_exists(__DIR__ . '/../../../../autoload.php')) {
    include(__DIR__ . '/../../../../autoload.php');
} elseif (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    include(__DIR__ . '/../../vendor/autoload.php');
}

return new class($argv) {

    /**
     * Console path
     * @var string|false
     */
    public string|bool $consolePath;

    /**
     * Action class
     * @var string
     */
    private string $actionClass;

    /**
     * Project path
     * @var
     */
    public $path;

    /**
     * Resouce path
     * @var
     */
    public $resourcesPath;

    /**
     * argv
     * @var array
     */
    public array $arg;

    public function __construct(array $argv)
    {
        $this->arg = Args::getArgs($argv);
        $this->consolePath = $this->arg['path'];
        $this->path = $this->arg['params']['projectPath'] ?? getcwd();
        $this->resourcesPath = realpath(__DIR__ . '/resources');

        if (!isset($this->arg['args'][0])) {
            $this->loadHelp();

            return;
        }

        $this->actionClass = '\Krzysztofzylka\MicroFramework\console\Action\\' . Strings::camelizeString($this->arg['args'][0], '_');

        if (!class_exists($this->actionClass)) {
            Prints::print('Action not found', false, true);
        }

        $class = new $this->actionClass($this);

        if (isset($this->arg['args'][0])) {
            if (!method_exists($class, $this->arg['args'][0])) {
                Prints::print('Action not found', false, true);
            }

            $class->{$this->arg['args'][0]}();
        }
    }

    /**
     * Load helper
     * @return void
     */
    public function loadHelp(): void
    {
        $class = '\Krzysztofzylka\MicroFramework\console\Action\Help';

        new $class();

        exit;
    }

    public function initKernel(): void
    {
        try {
            Kernel::initPaths($this->path);
            Kernel::autoload();
            Kernel::loadEnv();
            Kernel::configDatabaseConnect();
        } catch (Exception) {
        }
    }

};