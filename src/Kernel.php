<?php

namespace Krzysztofzylka\MicroFramework;

use DebugBar\DebugBarException;
use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Enum\DatabaseType;
use krzysztofzylka\DatabaseManager\Exception\ConnectException;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\File;
use Throwable;

/**
 * Kernel
 */
class Kernel
{

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
        'local_env' => null,
        'logs' => null,
        'assets' => null
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
            $url = explode('/', $_GET['url'] ?? '');
            $controller = $url[0] ?: $_ENV['DEFAULT_CONTROLLER'];
            $method = $url[1] ?? $_ENV['DEFAULT_METHOD'];
            $parameters = array_slice($url, 2);
            DebugBar::timeStop('run');
            DebugBar::addFrameworkMessage(['controller' => $controller, 'method' => $method, 'parameters' => $parameters, 'url' => $url], 'Run route');
            $route = new Route();
            $route->start($controller, $method, $parameters);
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
        self::$paths['controller'] = $this->projectPath . '/src/Controller';
        self::$paths['model'] = $this->projectPath . '/src/Model';
        self::$paths['view'] = $this->projectPath . '/src/View';
        self::$paths['env'] = $this->projectPath . '/.env';
        self::$paths['local_env'] = $this->projectPath . '/local.env';
        self::$paths['logs'] = $this->projectPath . '/storage/logs';
        self::$paths['assets'] = $this->projectPath . '/public/assets';

        foreach (self::$paths as $path) {
            if (str_contains($path, '.')) {
                if (!file_exists($path)) {
                    File::touch($path);
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
            DebugBar::timeStart('autoload', 'Autoload class');
            $path = File::repairPath($this->projectPath . DIRECTORY_SEPARATOR . $class_name . '.php');

            if (!file_exists($path)) {
                throw new NotFoundException();
            }

            include($path);
            DebugBar::timeStop('autoload');
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