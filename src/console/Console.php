<?php

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

    public function __construct(array $argv) {
        $this->consolePath = realpath($argv[0]);

        if (!isset($argv[1])) {
            Prints::print('No action', false, true);
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

    public function loadHelp() {
        $class = '\Krzysztofzylka\MicroFramework\console\Action\Help';

        new $class();
    }

};