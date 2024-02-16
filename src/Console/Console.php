<?php

namespace Krzysztofzylka\MicroFramework\Console;

use Krzysztofzylka\Console\Args as ConsoleLibrary;
use Krzysztofzylka\Console\Generator\Help;
use Krzysztofzylka\Console\Prints;
use Krzysztofzylka\MicroFramework\Console\Traits\InternalConsoleComponent;
use Krzysztofzylka\MicroFramework\Console\Traits\InternalConsoleCron;
use Krzysztofzylka\MicroFramework\Console\Traits\InternalConsoleMake;
use Krzysztofzylka\MicroFramework\Console\Traits\InternalConsoleMigration;
use Krzysztofzylka\MicroFramework\Console\Traits\InternalConsoleProject;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\Reflection\Reflection;
use ReflectionException;

/**
 * Console class
 */
class Console
{

    use InternalConsoleMigration;
    use InternalConsoleMake;
    use InternalConsoleComponent;
    use InternalConsoleCron;
    use InternalConsoleProject;

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
        switch ($this->args['args'][0] ?? null) {
            case 'init':
                $path = $this->path . (isset($this->args['args'][1]) ? ('/' . $this->args['args'][1]) : '');
                $this->initializeProject($path);
                break;
            case 'component':
                switch ($this->args['args'][1] ?? null) {
                    case 'list':
                        $this->getProjectComponentList();
                        break;
                    case 'install':
                        $this->installProjectComponent();
                        break;
                    default:
                        $this->renderHelp();
                }
                break;
            case 'cron':
                switch ($this->args['args'][1] ?? null) {
                    case 'run':
                        $this->cronRun();
                        break;
                    default:
                        $this->renderHelp();
                }
                break;
            case 'migration':
                switch ($this->args['args'][1] ?? null) {
                    case 'run':
                        $this->migrationRun();
                        break;
                    default:
                        $this->renderHelp();
                }
                break;
            case 'make':
                $this->print('Make element');

                if (!isset($this->args['args'][1])) {
                    $this->renderHelp();
                    exit;
                }

                $this->make($this->args['args'][1]);
                break;
            case 'help':
            default:
                $this->renderHelp();
                break;
        }
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
        $help->addHelp('cron run', 'Run CRON');
        $help->addHelp('migration run', 'Run migration');
        $help->addHelp('component list', 'Component list');
        #$help->addHelp('component install <name> [-secure false]', 'Install component');
        $help->addHeader('Make');
        $help->addHelp('make controller', 'Create controller');
        $help->addHelp('make controller::method', 'Create method in controller');
        $help->addHelp('make model', 'Create model');
        $help->addHeader('Parameters');
        $help->addHelp('-projectPath <path>', 'Define project path');
        $help->render();
    }

    /**
     * Get microframework path
     * @return string
     * @throws ReflectionException
     */
    private function getFrameworkPath(): string
    {
        return Reflection::getDirectoryPath(Kernel::class);
    }

    /**
     * Print string
     * @param string $value
     * @param string|null $color
     * @param bool $exit
     * @param bool $timestamp
     * @return void
     */
    private function print(string $value, ?string $color = null, bool $exit = false, bool $timestamp = true): void
    {
        Prints::print($value, $timestamp, $exit, $color);
    }

    /**s
     * Create input
     * @param string $label
     * @return string
     */
    private function input(string $label): string
    {
        $this->print(value: $label, timestamp: false);
        $input = fgets(STDIN);

        return trim($input);
    }

}