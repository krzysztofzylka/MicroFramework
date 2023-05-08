<?php

namespace Krzysztofzylka\MicroFramework;

include(__DIR__ . '/Extra/Functions.php');

use Exception;
use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Exception\ConnectException;
use Krzysztofzylka\MicroFramework\Api\Response;
use Krzysztofzylka\MicroFramework\Api\Secure;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NoAuthException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Account\Extra\AuthControl;
use Krzysztofzylka\MicroFramework\Extension\Debug\Debug;
use Krzysztofzylka\MicroFramework\Extension\Env\Env;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Statistic\Statistic;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;
use Krzysztofzylka\MicroFramework\Extension\Translation\Translation;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\_Array;
use krzysztofzylka\SimpleLibraries\Library\File;
use krzysztofzylka\SimpleLibraries\Library\Request;

/**
 * Kernel
 */
class Kernel
{

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
        'pa_controller' => null,
        'pa_model' => null,
        'pa_view' => null,
        'storage' => null,
        'logs' => null,
        'database_updater' => null,
        'config' => null,
        'env' => null
    ];

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
     * Init project
     * @param string $projectPath
     * @return void
     * @throws SimpleLibraryException
     */
    public static function initPaths(string $projectPath): void
    {
        self::$projectPath = $projectPath;
        self::$paths['public'] = $projectPath . '/public';
        self::$paths['controller'] = $projectPath . '/app/controller';
        self::$paths['model'] = $projectPath . '/app/model';
        self::$paths['view'] = $projectPath . '/app/view';
        self::$paths['api_controller'] = $projectPath . '/api/controller';
        self::$paths['pa_view'] = $projectPath . '/admin_panel/view';
        self::$paths['pa_controller'] = $projectPath . '/admin_panel/controller';
        self::$paths['pa_model'] = $projectPath . '/admin_panel/model';
        self::$paths['storage'] = $projectPath . '/storage';
        self::$paths['logs'] = self::$paths['storage'] . '/logs';
        self::$paths['database_updater'] = $projectPath . '/database_updater';
        self::$paths['assets'] = self::$paths['public'] . '/assets';
        self::$paths['config'] = $projectPath . '/config';
        self::$paths['env'] = $projectPath . '/env';

        foreach (self::$paths as $name => $path) {
            self::$paths[$name] = File::repairPath($path);

            File::mkdir($path, 0755);
        }
    }

    /**
     * Run framework
     * @return void
     * @throws ConnectException
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function run(): void
    {
        if ($_ENV['config_debug']) {
            Debug::$variables['site_load']['start'] = microtime(true);
        }

        if ($_ENV['config_show_all_errors']) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
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
                        Debug::$variables['site_load']['start'] = microtime(true);
                        DatabaseManager::$connection->setDebug(true);
                    }
                } catch (Exception){
                }
            }
        }

        $url = self::$url = isset($_GET['url']) ? ('/' . $_GET['url']) : $_ENV['config_default_page'];
        $extension = File::getExtension($url);

        new Statistic();

        if (!empty($extension)) {
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

        if ($_ENV['api_enabled'] && $controller === $_ENV['api_url']) {
            $controller = $explode[1] ?? $_ENV['config_default_controller'];
            $method = $explode[2] ?? $_ENV['config_default_method'];
            $arguments = array_slice($explode, 3);

            self::init($controller, $method, $arguments, ['api' => true]);
        } elseif ($_ENV['admin_panel_enabled'] && $controller === $_ENV['admin_panel_url']) {
            $controller = $explode[1] ?? $_ENV['config_default_controller'];
            $method = $explode[2] ?? $_ENV['config_default_method'];
            $arguments = array_slice($explode, 3);

            if (empty($controller)) {
                $controller = $method;
                $method = $_ENV['config_default_method'];
            }

            self::init($controller, $method, $arguments, ['admin_panel' => true]);
        } else {
            $method = $explode[1] ?? $_ENV['config_default_method'];
            $arguments = array_slice($explode, 2);

            self::init($controller, $method, $arguments);
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
     * @throws ConnectException
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
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function init(?string $controllerName = null, string $controllerMethod = 'index', array $controllerArguments = [], array $params = []): void
    {
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
                    'api' => $params['api'] ?? false,
                    'admin_panel' => $params['admin_panel'] ?? false
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
     * @throws NotFoundException
     * @throws NoAuthException
     * @throws MicroFrameworkException
     */
    public static function loadController(string $name, string $method = 'index', array $arguments = [], array $params = []): Controller
    {
        if (isset($params['admin_panel']) && $params['admin_panel']) {
            if (!$_ENV['admin_panel_enabled']) {
                throw new NotFoundException(__('micro-framework.kernel.adminpanel_disabled'));
            } elseif (!$_ENV['auth_control']) {
                throw new NotFoundException(__('micro-framework.kernel.authcontrol_disabled'));
            } elseif (!Account::isLogged()) {
                throw new NotFoundException(__('micro-framework.kernel.not_logged'));
            } elseif (!Account::$account['account']['admin']) {
                throw new NotFoundException(__('micro-framework.kernel.not_have_permission'));
            }

            $class = ObjectNameGenerator::controllerPaLocal($name);

            if (!class_exists($class)) {
                $class = ObjectNameGenerator::controllerPa($name);
            }
        } elseif (isset($params['api']) && $params['api']) {
            $class = ObjectNameGenerator::controllerApi($name);
        } else {
            $class = ObjectNameGenerator::controller($name);
        }

        if (!class_exists($class)) {
            throw new NotFoundException(__('micro-framework.kernel.controller_not_exists', ['controllerName' => $name]));
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

            if (isset($params['api']) && $params['api']) {
                /** @var ControllerApi $controller */
                $controller->secure = new Secure();
                $controller->response = new Response();

                $controller->secure->controller = $controller;
                $controller->response->controller = $controller;
            } else {
                $controller->htmlGenerator = new Html();
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

        call_user_func_array([$controller, $method], $arguments);

        if (!$controller->viewLoaded && (!isset($controller->params['api']) || $controller->params['api'] !== true)) {
            $controller->loadView();
        }

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
     * Get path
     * @param string|null $name controller / model / view
     * @return string|array|false
     */
    public static function getPath(?string $name): string|array|false
    {
        if (is_null($name)) {
            return self::$paths;
        }

        if (!_Array::inArrayKeys($name, self::$paths)) {
            return false;
        }

        return self::$paths[$name];
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

}