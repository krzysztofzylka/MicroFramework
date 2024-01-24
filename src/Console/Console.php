<?php

namespace Krzysztofzylka\MicroFramework\Console;

use Cron\CronExpression;
use Krzysztofzylka\Console\Args as ConsoleLibrary;
use Krzysztofzylka\Console\Generator\Help;
use Krzysztofzylka\Console\Generator\Table;
use Krzysztofzylka\Console\Prints;
use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Libs\Log\Log;
use Krzysztofzylka\Reflection\Reflection;
use ReflectionException;

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
            case 'help':
            default:
                $this->renderHelp();
                break;
        }
    }

    private function cronRun(): void
    {
        $this->print('Cron start');
        $cronFile = File::repairPath($this->path . '/cron.json');

        if (!file_exists($cronFile)) {
            $this->print('Not found cron.json file in ' . $this->path, 'red', true);
        }

        try {
            new Kernel($this->path);

            $cron = json_decode(file_get_contents($cronFile), true);

            foreach ($cron as $key => $schedule) {
                $this->print('Execute schedule ' . $key);
                $cronExpression = new CronExpression($schedule['time']);

                if ($cronExpression->isDue()) {
                    if (!isset($schedule['model']) || !isset($schedule['method'])) {
                        $this->print('Shedule not found model and method params', 'yellow');

                        continue;
                    }

                    try {
                        $controller = new Controller();
                        $model = $controller->loadModel($schedule['model']);
                        call_user_func_array([$model, $schedule['method']], $schedule['args'] ?? []);
                        $this->print('Success', 'green');
                    } catch (\Throwable $throwable) {
                        Log::log('Failed execute schedule', 'ERROR', ['message' => $throwable->getMessage()]);
                        $this->print('Failed execute schedule', 'yellow');

                        continue;
                    }
                }
            }
        } catch (\Throwable $throwable) {
            Log::log('Cron error', 'ERROR', ['message' => $throwable->getMessage()]);
            $this->print('Failed read cron.json file', 'red', true);
        }
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
     * Install component
     * @return void
     */
    private function installProjectComponent(): void
    {
        $componentName = $this->args['args'][2];
        $secured = isset($this->args['params']['secure']) ? (string)$this->args['params']['secure'] !== 'false' : true;

        $this->print('Install component: ' . $componentName);

        if (!$secured) {
            $this->print('Unsecured installer', 'yellow');
        }

        try {
            $packageName = $componentName;
            $this->print('Require composer package: ' . $packageName);
            $command = 'cd ../; composer require ' . $packageName;
            shell_exec($command);
            $packagePath = realpath(__DIR__ . '/../../vendor/' . $packageName);

            if (!file_exists($packagePath)) {
                $this->print('Failed install component', 'red', true);
            }

            $composerData = json_decode(file_get_contents($packagePath . '/composer.json'), true);
            $class = "\\" . array_key_first($composerData['autoload']['psr-4']) . 'Component';
            $componentList = json_decode(file_get_contents($this->path . '/component.json'), true);

            if (class_exists($class) && !in_array($class, $componentList['components'])) {
                $this->print('Add component to list');

                $componentList['components'][] = $class;

                file_put_contents($this->path . '/component.json', json_encode($componentList));
            }
        } catch (\Throwable $exception) {
            $this->print('Failed install component', 'red');
            $this->print($exception->getMessage(), 'red', true);
        }

        $this->print('Success install component', 'green');
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
        $help->addHelp('component list', 'Component list');
        $help->addHelp('component install <name> [-secure false]', 'Install component');
        $help->addHeader('Parameters');
        $help->addHelp('-projectPath <path>', 'Define project path');
        $help->render();
    }

}