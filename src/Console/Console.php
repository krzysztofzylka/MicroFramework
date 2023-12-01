<?php

namespace Krzysztofzylka\MicroFramework\Console;

use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Console\Args as ConsoleLibrary;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;
use krzysztofzylka\SimpleLibraries\Library\File;

/**
 * Console class
 */
class Console
{

    /**
     * Args
     * @var array
     */
    private array $args;

    /**
     * Project path
     * @var string
     */
    private string $path;

    /**
     * MicroFramework path
     * @var string
     */
    private string $frameworkPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->args = ConsoleLibrary::getArgs($_SERVER['argv']);
        $this->path = $_SERVER['PWD'];
        $this->frameworkPath = $this->getFrameworkPath();
    }

    /**
     * Run console
     * @return void
     */
    public function run(): void
    {
        switch ($this->args['args'][0]) {
            case 'init':
                $path = $this->path . (isset($this->args['args'][1]) ? ('/' . $this->args['args'][1]) : '');
                $this->initializeProject($path);
                break;
        }
    }

    /**
     * Get microframework path
     * @return string
     */
    private function getFrameworkPath(): string
    {
        $reflection = new \ReflectionClass(Kernel::class);
        return dirname($reflection->getFileName());
    }

    /**
     * Print string
     * @param string $value
     * @param string|null $color
     * @param bool $exit
     * @return void
     */
    private function print(string $value, ?string $color = null, bool $exit = false): void
    {
        Prints::print($value, true, $exit, $color);
    }

    /**
     * Initialize project
     * @param string $path
     * @return void
     */
    private function initializeProject(string $path): void
    {
        $this->print('Initialize project');
        $this->print('Project path: ' . $path);

        try {
            File::mkdir([
                $path,
                $path . '/public'
            ], 0755);
            File::copyDirectory($this->frameworkPath . '/Console/resources/public', $path . '/public');
            new Kernel($path);
        } catch (\Throwable $exception) {
            $this->print('Fail initialize project', 'red');
            $this->print($exception->getMessage(), 'red', true);
        }

        $this->print('Success initialize project', 'green');
    }

}