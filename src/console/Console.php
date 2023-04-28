<?php

use config\Config;
use Krzysztofzylka\MicroFramework\Kernel;
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
    private string|bool $consolePath;

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
     * @var
     */
    public $arg;

    public function __construct(array $argv)
    {
        $this->consolePath = realpath($argv[0]);
        $this->path = getcwd();
        $this->resourcesPath = realpath(__DIR__ . '/resources');
        $this->arg = $argv;

        if (!isset($argv[1])) {
            $this->loadHelp();
        }

        $this->actionClass = '\Krzysztofzylka\MicroFramework\console\Action\\' . Strings::camelizeString($argv[1], '_');

        if (!class_exists($this->actionClass)) {
            Prints::print('Action not found', false, true);
        }

        try {
            $class = new $this->actionClass($this);

            if (isset($argv[2])) {
                if (!method_exists($class, $argv[2])) {
                    throw new Exception('Action not exists');
                }

                $class->{$argv[2]}();
            }
        } catch (Exception) {
            $this->loadHelp();
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
            Kernel::setConfig(new Config());
            Kernel::configDatabaseConnect();
        } catch (Exception) {
        }
    }

};