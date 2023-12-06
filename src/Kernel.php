<?php

namespace Krzysztofzylka\MicroFramework;

use DebugBar\DebugBarException;
use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Enum\DatabaseType;
use krzysztofzylka\DatabaseManager\Exception\ConnectException;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use Krzysztofzylka\MicroFramework\Component\Loader;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\File;
use Throwable;

include_once(__DIR__ . '/Extension/Functions/functions.php');

/**
 * Kernel
 */
class Kernel
{

    /**
     * Loader instance
     * @var Loader
     */
    private Loader $loaderInstance;

    /**
     * Paths
     * @var array
     */
    private static array $paths = [
        'public' => null,
        'controller' => null,
        'model' => null,
        'view' => null,
        'env' => null,
        'storage' => null,
        'local_env' => null,
        'logs' => null,
        'src' => null,
        'assets' => null,
        'components_config' => null
    ];

    /**
     * Project path
     * @var string
     */
    private string $projectPath;

    /**
     * Get path
     * @param string $name path name
     * @return string
     */
    public static function getPath(string $name): string
    {
        return self::$paths[$name];
    }

    /**
     * Init controller
     * @param string $projectPath project path
     * @throws MicroFrameworkException
     */
    public function __construct(string $projectPath)
    {
        try {

            $this->projectPath = realpath($projectPath);
            $this->initPaths();
            $this->loadEnv();
            $this->initConfigurations();
            $this->autoload();
            $this->connectDatabase();
            $this->loaderInstance = new Loader();
            Log::log('Start kernel');
        } catch (Throwable $exception) {
            throw new MicroFrameworkException($exception->getMessage());
        }
    }

    /**
     * Run kernel
     * @return void
     * @throws Throwable
     */
    public function run(): void
    {
        try {
            DebugBar::timeStart('run', 'Generate run data');
            $url = explode('/', htmlspecialchars($_GET['url'] ?? '', ENT_QUOTES));
            $controller = $url[0] ?: $_ENV['DEFAULT_CONTROLLER'];
            $method = $url[1] ?? $_ENV['DEFAULT_METHOD'];
            $parameters = array_slice($url, 2);
            DebugBar::timeStop('run');
            DebugBar::addFrameworkMessage(['controller' => $controller, 'method' => $method, 'parameters' => $parameters, 'url' => $url], 'Run route');
            $route = new Route();
            $route->start($controller, $method, $parameters);
            $this->loaderInstance->initAfterComponents();
        } catch (Throwable $exception) {
            Log::log($exception->getMessage(), 'ERROR');
            DebugBar::addThrowable($exception);

            throw $exception;
        }
    }

    /**
     * Set paths
     * @return void
     * @throws SimpleLibraryException
     */
    private function initPaths(): void
    {
        self::$paths['public'] = $this->projectPath . '/public';
        self::$paths['src'] = $this->projectPath . '/src';
        self::$paths['controller'] = self::$paths['src']  . '/Controller';
        self::$paths['model'] = self::$paths['src']  . '/Model';
        self::$paths['view'] = self::$paths['src']  . '/View';
        self::$paths['env'] = $this->projectPath . '/.env';
        self::$paths['local_env'] = $this->projectPath . '/local.env';
        self::$paths['storage'] = $this->projectPath . '/storage';
        self::$paths['logs'] = self::$paths['storage'] . '/logs';
        self::$paths['assets'] = $this->projectPath . '/public/assets';
        self::$paths['components_config'] = $this->projectPath . '/component.json';

        foreach (self::$paths as $key => $path) {
            if (str_contains($path, '.')) {
                if (!file_exists($path)) {
                    $value = null;

                    if ($key === 'components_config') {
                        $value = json_encode(['components' => []]);
                    }

                    File::touch($path, $value);
                }
            } else {
                if (!is_dir($path)) {
                    File::mkdir($path, 0755);
                }
            }
        }
    }

    /**
     * Load ENV file
     * @return void
     */
    private function loadEnv(): void
    {
        (new Extension\Env\Env(__DIR__ . '/Default/.env'))->load();
        (new Extension\Env\Env(self::$paths['env']))->load();

        if (file_exists(self::$paths['local_env'])) {
            (new Extension\Env\Env(self::$paths['local_env']))->load();
        }
    }

    /**
     * Init configurations
     * @return void
     * @throws SimpleLibraryException
     * @throws DebugBarException
     */
    private function initConfigurations(): void
    {
        if (!is_null($_ENV['TIMEZONE'])) {
            date_default_timezone_set($_ENV['TIMEZONE']);
        }

        if ($_ENV['DEBUG']) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            (new DebugBar())->init();
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        }
    }

    /**
     * Autoload
     * @return void
     * @throws NotFoundException
     */
    private function autoload(): void
    {
        spl_autoload_register(function ($class_name) {
            if (file_exists(__DIR__ . '/Extension/View/Plugins/' . $class_name . '.php')) {
                include(__DIR__ . '/Extension/View/Plugins/' . $class_name . '.php');
                DebugBar::timeStop('autoload');

                return;
            }

            $path = File::repairPath($this->projectPath . DIRECTORY_SEPARATOR . $class_name . '.php');

            if (!file_exists($path)) {
                throw new NotFoundException($class_name);
            }

            include($path);
        });
    }

    /**
     * Connect to database
     * @return void
     * @throws DatabaseManagerException
     * @throws DebugBarException
     * @throws ConnectException
     */
    private function connectDatabase(): void
    {
        if (!$_ENV['DATABASE']) {
            return;
        }

        DebugBar::addFrameworkMessage('Connect to database', 'Database');

        DebugBar::timeStart('database', 'Connect to database');
        $connection = new DatabaseConnect();
        $connection->setType(
            match ($_ENV['DATABASE_DRIVER']) {
                'mysql' => DatabaseType::mysql,
                'sqlite' => DatabaseType::sqlite
            }
        );
        $connection->setCharset($_ENV['DATABASE_CHARSET']);
        $connection->setHost($_ENV['DATABASE_HOST']);
        $connection->setDatabaseName($_ENV['DATABASE_NAME']);
        $connection->setPassword($_ENV['DATABASE_PASSWORD']);
        $connection->setUsername($_ENV['DATABASE_USERNAME']);

        try {
            $manager = new DatabaseManager();
            $manager->connect($connection);
        } catch (DatabaseManagerException $exception) {
            DebugBar::addThrowable($exception);
            DebugBar::addFrameworkMessage($exception->getHiddenMessage(), 'ERROR');

            throw $exception;
        }

        DebugBar::timeStop('database');
        DebugBar::addPdoCollection();
    }

}