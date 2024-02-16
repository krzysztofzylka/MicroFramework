<?php

namespace Krzysztofzylka\MicroFramework\Console;

use Cron\CronExpression;
use Krzysztofzylka\Console\Args as ConsoleLibrary;
use Krzysztofzylka\Console\Generator\Help;
use Krzysztofzylka\Console\Generator\Table;
use Krzysztofzylka\Console\Prints;
use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Kernel;
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

    private function make(string $name): void
    {
        switch ($name) {
            case 'controller':
                $controllerName = $this->input('Controller name:');
                $controllerDirectory = $this->path . '/src/Controller';
                $controllerPath = $controllerDirectory . '/' . $controllerName . '.php';

                if (file_exists($controllerPath)) {
                    $this->print('Controller ' . $controllerName . ' is already exists', color: 'red', exit: true);
                }

                File::mkdir([
                    $controllerDirectory,
                    $this->path . '/src/View/' . $controllerName
                ]);

                $controllerContent = file_get_contents($this->frameworkPath . '/Console/resources/make/controller.php');
                $controllerContent = str_replace('controllerName', $controllerName, $controllerContent);

                file_put_contents($controllerPath, $controllerContent);

                $this->print('Success create controller ' . $controllerName, color: 'green');

                break;
            case 'controller::method':
                Kernel::$silent = true;
                new Kernel($this->path);

                $controllerName = $this->input('Controller name:');
                $controllerDirectory = $this->path . '/src/Controller';
                $controllerPath = $controllerDirectory . '/' . $controllerName . '.php';

                if (!file_exists($controllerPath)) {
                    $this->print('Controller ' . $controllerName . ' not exists', color: 'red', exit: true);
                }

                $methodName = $this->input('Method name:');
                $methodList = array_column(Reflection::getClassMethods('\src\Controller\\' . $controllerName), 'method');

                if (in_array($methodName, $methodList)) {
                    $this->print('Method ' . $methodName . ' is already exists', color: 'red', exit: true);
                }

                $makeFile = 'method';
                $createView = $this->input('Create view (y/n)');

                if ($createView === 'y') {
                    File::mkdir($this->path . '/src/View/' . $controllerName);
                    file_put_contents($this->path . '/src/View/' . $controllerName . '/' . $methodName . '.twig', '');
                    $makeFile = 'method_view';
                }

                $controllerContent = file_get_contents($controllerPath);
                $lastBracketPosition = $this->findLastBracketPosition($controllerContent) - 1;
                $methodContent = file_get_contents($this->frameworkPath . '/Console/resources/make/controller/' . $makeFile . '.php') . "\n\n";
                $methodContent = str_replace('methodName', $methodName, $methodContent);

                $newControllerContent = substr_replace(
                    $controllerContent,
                    $methodContent,
                    $lastBracketPosition-1,
                    0
                );

                file_put_contents($controllerPath, $newControllerContent);

                $this->print('Success create method ' . $methodName . ' in controller ' . $controllerName, color: 'green');
                break;
            case 'model':
                $modelName = $this->input('Model name:');
                $modelDirectory = $this->path . '/src/Model';
                $modelPath = $modelDirectory . '/' . $modelName . '.php';

                if (file_exists($modelPath)) {
                    $this->print('Model ' . $modelName . ' is already exists', color: 'red', exit: true);
                }

                File::mkdir($modelDirectory);

                $modelContent = file_get_contents($this->frameworkPath . '/Console/resources/make/model.php');
                $modelContent = str_replace('modelName', $modelName, $modelContent);

                file_put_contents($modelPath, $modelContent);

                $this->print('Success create model ' . $modelName, color: 'green');

                break;
            default:
                $this->renderHelp();
                break;
        }
    }

    private function findLastBracketPosition(string $content): false|int
    {
        $lastBracketPosition = 0;
        $appendCount = 0;

        foreach (explode("\n", $content) as $line => $data) {
            if (trim($data) === '}') {
                $lastBracketPosition += $appendCount + strlen($data) + 1;
                $appendCount = 0;
            } else {
                $appendCount += strlen($data) + 1;
            }
        }

        return $lastBracketPosition === 0 ? false : $lastBracketPosition;
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
        #$help->addHelp('component install <name> [-secure false]', 'Install component');
        $help->addHeader('Make');
        $help->addHelp('make controller', 'Create controller');
        $help->addHelp('make controller::method', 'Create method in controller');
        $help->addHelp('make model', 'Create model');
        $help->addHeader('Parameters');
        $help->addHelp('-projectPath <path>', 'Define project path');
        $help->render();
    }

}