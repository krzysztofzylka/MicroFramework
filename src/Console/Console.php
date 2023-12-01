<?php

namespace Krzysztofzylka\MicroFramework\Console;

use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Console\Args as ConsoleLibrary;
use krzysztofzylka\SimpleLibraries\Library\Console\Generator\Help;
use krzysztofzylka\SimpleLibraries\Library\Console\Generator\Table;
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

        if (isset($this->args['params']['projectPath'])) {
            $this->path = $this->args['params']['projectPath'];
        }
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
            case 'component':
                switch ($this->args['args'][1]) {
                    case 'list':
                        $this->getProjectComponentList();
                        break;
                    default:
                        $this->renderHelp();
                }
                break;
            case 'help':
            default:
                $this->renderHelp();
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

    /**
     * Project component lists
     * @return void
     */
    private function getProjectComponentList(): void
    {
        $componentFile = $this->path . '/component.json';

        if (!file_exists($componentFile)) {
            $this->print('Not found component.json', 'red', true);
        }

        $components = json_decode(file_get_contents($componentFile), true);
        $componentList = [];

        foreach ($components['components'] as $component) {
            $componentList[] = [
                'path' => $component
            ];
        }

        $table = new Table();
        $table->setData($componentList);
        $table->addColumn('Component', 'path');
        $table->render();
    }

    /**
     * Render help
     * @return void
     */
    private function renderHelp(): void
    {
        $help = new Help();
        $help->addHeader('Help');
        $help->addHelp('init', 'Initialize project');
        $help->addHelp('component list', 'Component list');
        $help->addHeader('Parameters');
        $help->addHelp('-projectPath <path>', 'Define project path');
        $help->render();
    }

}