<?php

namespace Krzysztofzylka\MicroFramework;

include(__DIR__ . '/Extra/Functions.php');

use DateTime;
use DateTimeZone;
use Exception;
use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Exception\ConditionException;
use krzysztofzylka\DatabaseManager\Exception\ConnectException;
use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Exception\TableException;
use Krzysztofzylka\MicroFramework\Api\Response;
use Krzysztofzylka\MicroFramework\Api\Secure;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NoAuthException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Account\Extra\AuthControl;
use Krzysztofzylka\MicroFramework\Extension\CommonFile\CommonFile;
use Krzysztofzylka\MicroFramework\Extension\Env\Env;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Extension\Memcache\Memcache;
use Krzysztofzylka\MicroFramework\Extension\Statistic\Statistic;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;
use Krzysztofzylka\MicroFramework\Extension\Translation\Translation;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;
use Krzysztofzylka\MicroFramework\Extra\ObjectTypeEnum;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\_Array;
use krzysztofzylka\SimpleLibraries\Library\File;
use krzysztofzylka\SimpleLibraries\Library\PHPDoc;
use krzysztofzylka\SimpleLibraries\Library\Request;
use Throwable;

/**
 * Kernel
 */
class Kernel
{

    /**
     * Parametry kontrolera
     * @var array
     */
    public static array $controllerParams;

    /**
     * Url
     * @var string
     */
    public static string $url;

    /**
     * Project path
     * @var string
     */
    private static string $projectPath;

    /**
     * Paths
     * @var array
     */
    private static array $paths = [
        'public' => null,
        'assets' => null,
        'controller' => null,
        'model' => null,
        'view' => null,
        'api_controller' => null,
        'storage' => null,
        'logs' => null,
        'database_updater' => null,
        'config' => null,
        'env' => null,
        'service' => null
    ];

    /**
     * Init project
     * @param string $projectPath
     * @param bool $createPath
     * @return void
     * @throws SimpleLibraryException
     */
    public static function initPaths(string $projectPath, bool $createPath = true): void
    {
        self::$projectPath = $projectPath;
        self::$paths['public'] = $projectPath . '/public';
        self::$paths['controller'] = $projectPath . '/app/controller';
        self::$paths['model'] = $projectPath . '/app/model';
        self::$paths['view'] = $projectPath . '/app/view';
        self::$paths['api_controller'] = $projectPath . '/api/controller';
        self::$paths['storage'] = $projectPath . '/storage';
        self::$paths['logs'] = self::$paths['storage'] . '/logs';
        self::$paths['database_updater'] = $projectPath . '/database_updater';
        self::$paths['assets'] = self::$paths['public'] . '/assets';
        self::$paths['config'] = $projectPath . '/config';
        self::$paths['env'] = $projectPath . '/env';
        self::$paths['service'] = $projectPath . '/service';

        if ($createPath) {
            foreach (self::$paths as $name => $path) {
                self::$paths[$name] = File::repairPath($path);

                File::mkdir($path, 0755);
            }
        }
    }

    /**
     * Run framework
     * @return void
     * @throws DatabaseException
     * @throws Throwable
     */
    public static function run(): void
    {
        if (!is_null($_ENV['config_timezone'])) {
            date_default_timezone_set($_ENV['config_timezone']);
        }

        if ($_ENV['config_show_all_errors']) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        }

        if ($_ENV['config_memcache']) {
            Memcache::run();
        }

        Translation::getTranslationFile(__DIR__ . '/Translations/' . $_ENV['config_translation'] . '.yaml');

        self::errorHandler();

        self::configDatabaseConnect();

        if (Account::isLogged()) {
            new Account();

            if ($_ENV['config_debug_account_ids'] !== false) {
                try {
                    $accountIds = explode(',', $_ENV['config_debug_account_ids']);

                    if (isset(Account::$accountId) && in_array(Account::$accountId, $accountIds)) {
                        $_ENV['config_debug'] = true;
                        DatabaseManager::$connection->setDebug(true);
                    }
                } catch (Exception) {
                }
            }
        }

        $url = self::$url = isset($_GET['url']) ? ('/' . $_GET['url']) : $_ENV['config_default_page'];
        $extension = File::getExtension($url);
        $controllerName = isset($_GET['url']) ? htmlspecialchars(explode('/', $_GET['url'])[0]) : '';

        new Statistic();

        if (!empty($extension) && !in_array($controllerName, ['public_files', 'common_file'])) {
            if (!file_exists($url)) {
                http_response_code(404);
                exit;
            }
        }

        $explode = explode('/', $url);

        if (empty($explode[0])) {
            unset($explode[0]);
            $explode = array_values($explode);
        }

        $controller = $explode[0];

        try {
            if ($_ENV['api_enabled'] && $controller === $_ENV['api_url']) {
                $controller = $explode[1] ?? $_ENV['config_default_controller'];
                $method = $explode[2] ?? $_ENV['config_default_method'];
                $arguments = array_slice($explode, 3);

                self::init($controller, $method, $arguments, ['api' => true]);
            } else {
                $method = $explode[1] ?? $_ENV['config_default_method'];
                $arguments = array_slice($explode, 2);

                self::init($controller, $method, $arguments);
            }
        } catch (Throwable $throwable) {
            if ($throwable instanceof RuntimeError && $throwable->getPrevious()) {
                $throwable = $throwable->getPrevious();
            }

            throw $throwable;
        }
    }

    /**
     * Error handler
     * @return void
     */
    public static function errorHandler(): void
    {
        set_error_handler('\Krzysztofzylka\MicroFramework\Extension\ErrorHandler\ErrorHandler::errorHandler');
        register_shutdown_function('Krzysztofzylka\MicroFramework\Extension\ErrorHandler\ErrorHandler::shutdownHandler');
    }

    /**
     * Connect to database
     * @return void
     * @throws DatabaseException
     */
    public static function configDatabaseConnect(): void
    {
        if ($_ENV['database_enabled']) {
            $databaseConnect = (new DatabaseConnect())
                ->setHost($_ENV['database_host'])
                ->setUsername($_ENV['database_username'])
                ->setPassword($_ENV['database_password'])
                ->setDatabaseName($_ENV['database_name']);

            if ($_ENV['config_debug']) {
                $databaseConnect->setDebug(true);
            }

            try {
                $databaseManager = new DatabaseManager();
                $databaseManager->connect($databaseConnect);

                if (!is_null($_ENV['config_timezone'])) {
                    $time_zone = (new DateTime('now', new DateTimeZone($_ENV['config_timezone'])))->format('P');
                    $sql = 'SET time_zone="' . $time_zone . '";';
                    DatabaseManager::setLastSql($sql);
                    $databaseManager->query($sql);
                }
            } catch (ConnectException $exception) {
                throw new DatabaseException($exception->getHiddenMessage());
            }
        }
    }

    /**
     * Init framework
     * @param ?string $controllerName
     * @param string $controllerMethod
     * @param array $controllerArguments
     * @param array $params additional init params
     * @return void
     * @throws ConditionException
     * @throws DatabaseException
     * @throws MicroFrameworkException
     * @throws NoAuthException
     * @throws NotFoundException
     * @throws SelectException
     * @throws TableException
     * @throws Throwable
     * @throws ViewException
     */
    public static function init(?string $controllerName = null, string $controllerMethod = 'index', array $controllerArguments = [], array $params = []): void
    {
        if ($_ENV['update_block_api'] && isset($params['api']) && $params['api']) {
            $response = new Response();
            $response->json([
                'error' => [
                    'message' => 'System update',
                    'code' => 500
                ]
            ]);
        } elseif ($_ENV['update_block_site']
            && (!isset($params['api'])
                || isset($params['api']) && !$params['api'])
        ) {
            $view = new View();
            echo $view->render([], $_ENV['update_view']);

            exit;
        }

        self::$controllerParams = $params;

        if (!self::$projectPath) {
            throw new MicroFrameworkException(__('micro-framework.kernel.project_not_defined'), 500);
        }

        if (!is_null($controllerName)) {
            self::loadController(
                $controllerName,
                $controllerMethod,
                $controllerArguments,
                [
                    'api' => $params['api'] ?? false
                ]
            );
        }
    }

    /**
     * Load controller
     * @param string $name controller name
     * @param string $method method
     * @param array $arguments method arguments
     * @param array $params additional params for loader
     * @return Controller
     * @throws DatabaseException
     * @throws ViewException
     * @throws MicroFrameworkException
     * @throws NoAuthException
     * @throws NotFoundException
     * @throws Throwable
     * @throws ConditionException
     * @throws SelectException
     * @throws TableException
     */
    public static function loadController(
        string $name,
        string $method = 'index',
        array $arguments = [],
        array $params = []
    ): Controller
    {
        Debug::startTime();
        if (empty($params)) {
            $params = $_SESSION['controllerParams'];
        } else {
            $_SESSION['controllerParams'] = $params;
        }

        if (isset($params['api']) && $params['api']) {
            $class = ObjectNameGenerator::controller($name, ObjectTypeEnum::API);
        } else {
            $class = ObjectNameGenerator::controller($name, ObjectTypeEnum::APP_LOCAL);

            if (!class_exists($class)) {
                $class = ObjectNameGenerator::controller($name, ObjectTypeEnum::APP);
            }
        }

        if (!class_exists($class)) {
            throw new NotFoundException(__('micro-framework.kernel.controller_not_exists', ['controllerName' => $name]));
        }

        $ajaxProtect = (bool)(PHPDoc::getClassMethodComment($class, $method, 'ajax')[0] ?? false);

        if ($ajaxProtect && !Request::isAjaxRequest()) {
            throw new NotFoundException();
        }

        AuthControl::run($class, $method, isset($params['api']) && $params['api']);

        try {
            /** @var Controller $controller */
            $controller = new $class();
            $controller->name = $name;
            $controller->method = $method;
            $controller->arguments = $arguments;
            $controller->data = self::getData();
            $controller->params = $params;
            $controller->commonFile = new CommonFile();

            if (isset($params['api']) && $params['api']) {
                /** @var ControllerApi $controller */
                $controller->secure = new Secure();
                $controller->response = new Response();

                $controller->secure->controller = $controller;
                $controller->response->controller = $controller;

                $controller->_autoAuth();
            } else {
                $controller->table = new Table();
                $controller->table->controller = $controller;
                $controller->table->data = $controller->data;
                $controller->table->init();
            }

            if (!method_exists($controller, $method)) {
                throw new NotFoundException(__('micro-framework.kernel.method_is_controller_not_exists', ['methodName' => $method, 'controllerName' => $name]));
            }
        } catch (NotFoundException $exception) {
            throw new NotFoundException($exception->getHiddenMessage());
        } catch (Exception $exception) {
            throw new MicroFrameworkException($exception->getMessage(), $exception->getCode());
        }

        try {
            call_user_func_array([$controller, $method], $arguments);
        } catch (Throwable $exception) {
            Log::log(
                'Błąd wywołania kontrolera',
                'WARNING',
                ['exception' => $exception->getMessage(), 'trace' => $exception->getTrace()]
            );

            throw $exception;
        }

        if (!$controller->viewLoaded
            && (!isset($controller->params['api']) || $controller->params['api'] !== true)
            && !in_array($controller->layout, ['none', 'table'])
        ) {
            $controller->loadView();
        } elseif ($controller->layout === 'table') {
            if (!$controller->table->isRender) {
                echo $controller->table->render();
            }
        }

        Debug::endTime('controller_' . $name);

        return $controller;
    }

    /**
     * Get post data
     * @return ?array
     */
    public static function getData(): ?array
    {
        if (!Request::isPost()) {
            return null;
        }

        return Request::getAllPostEscapeData();
    }

    /**
     * Autoload
     * @return void
     * @throws NotFoundException
     */
    public static function autoload(): void
    {
        spl_autoload_register(function ($class_name) {
            $path = File::repairPath(self::getProjectPath() . DIRECTORY_SEPARATOR . $class_name . '.php');

            if (!file_exists($path)) {
                throw new NotFoundException();
            }

            include($path);
        });
    }

    /**
     * Get project path
     * @return string
     */
    public static function getProjectPath(): string
    {
        return self::$projectPath;
    }

    /**
     * Database connect
     * @param DatabaseConnect $databaseConnect
     * @return void
     * @throws DatabaseException
     */
    public static function databaseConnect(DatabaseConnect $databaseConnect): void
    {
        try {
            $databaseManager = new DatabaseManager();
            $databaseManager->connect($databaseConnect);
        } catch (ConnectException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Load ENV files
     * @return void
     */
    public static function loadEnv(): void
    {
        Env::createFromDirectory(__DIR__ . '/Extension/Env/Default');
        Env::createFromDirectory(Kernel::getPath('env'));
    }

    /**
     * Get path
     * @param string|null $name controller / model / view
     * @return mixed
     */
    public static function getPath(?string $name): mixed
    {
        if (is_null($name)) {
            return self::$paths;
        }

        if (!_Array::inArrayKeys($name, self::$paths)) {
            return false;
        }

        return self::$paths[$name];
    }

}
