<?php

namespace Krzysztofzylka\MicroFramework;

include(__DIR__ . '/Extra/Functions.php');

use Exception;
use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Exception\ConnectException;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NoAuthException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Account\Extra\AuthControl;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;
use krzysztofzylka\SimpleLibraries\Library\File;
use krzysztofzylka\SimpleLibraries\Library\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

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
        'api_controller' => null,
        'model' => null,
        'view' => null,
        'pa_view' => null,
        'storage' => null,
        'logs' => null,
        'database_updater' => null
    ];

    /**
     * Config
     * @var object
     */
    private static object $config;

    /**
     * Init project
     * @param string $projectPath
     * @return void
     */
    public static function create(string $projectPath): void
    {
        self::$projectPath = $projectPath;
        self::$paths['public'] = realpath($projectPath . '/public');
        self::$paths['controller'] = $projectPath . '/controller';
        self::$paths['api_controller'] = $projectPath . '/api_controller';
        self::$paths['pa_controller'] = $projectPath . '/pa_controller';
        self::$paths['model'] = $projectPath . '/model';
        self::$paths['view'] = $projectPath . '/view';
        self::$paths['pa_view'] = $projectPath . '/pa_view';
        self::$paths['storage'] = $projectPath . '/storage';
        self::$paths['logs'] = self::$paths['storage'] . '/logs';
        self::$paths['database_updater'] = $projectPath . '/database_updater';
        self::$paths['assets'] = self::$paths['public'] . '/assets';

        foreach (self::$paths as $name => $path) {
            self::$paths[$name] = File::repairPath($path);

            File::mkdir($path, 0755);
        }

        if (!file_exists(self::$paths['public'] . '/.htaccess')) {
            copy(__DIR__ . '/Init/.htaccess', self::$paths['public'] . '/.htaccess');
        }
    }

    /**
     * init view variables
     * @throws LoaderError
     */
    public static function initViewVariables(): void
    {
        View::$filesystemLoader = new FilesystemLoader(self::getPath('view'));
        View::$filesystemLoader->addPath(__DIR__ . '/Twig/template');
        View::$filesystemLoader->addPath(__DIR__ . '/Twig/email_template');
        View::$filesystemLoader->addPath(__DIR__ . '/Extension/Twig/Macros');
        View::$environment = new Environment(View::$filesystemLoader, ['debug' => true]);
        View::$environment->addExtension(new DebugExtension());
        View::$environment->setCache(false);
    }

    /**
     * Run framework
     * @return void
     * @throws ConnectException
     * @throws LoaderError
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function run(): void
    {
        if (!isset(self::$config)) {
            self::$config = new ConfigDefault();
        }

        self::errorHandler();

        self::configDatabaseConnect();

        if (Account::isLogged()) {
            new Account();
        }

        $url = $_GET['url'] ?? self::getConfig()->defaultPage;
        $explode = explode('/', $url);

        if (empty($explode[0])) {
            unset($explode[0]);
            $explode = array_values($explode);
        }

        $controller = $explode[0];

        if (self::$config->api && $controller === self::$config->apiUri) {
            $controller = $explode[1];
            $method = $explode[2] ?? self::getConfig()->defaultMethod;
            $arguments = array_slice($explode, 3);

            self::init($controller, $method, $arguments, ['api' => true]);
        } else {
            $method = $explode[1] ?? self::getConfig()->defaultMethod;
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
     */
    public static function configDatabaseConnect(): void
    {
        if (self::$config->database) {
            $databaseConnect = (new DatabaseConnect())
                ->setHost(self::$config->databaseHost)
                ->setUsername(self::$config->databaseUsername)
                ->setPassword(self::$config->databasePassword)
                ->setDatabaseName(self::$config->databaseName);

            if (self::getConfig()->debug) {
                $databaseConnect->setDebug(true);
            }

            $databaseManager = new DatabaseManager();
            $databaseManager->connect($databaseConnect);
        }
    }

    /**
     * Get config
     * @return ConfigDefault
     */
    public static function getConfig(): object
    {
        return self::$config;
    }

    /**
     * Set config
     * @param object $config
     * @return void
     */
    public static function setConfig(object $config): void
    {
        self::$config = $config;
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
     * @throws LoaderError
     */
    public static function init(?string $controllerName = null, string $controllerMethod = 'index', array $controllerArguments = [], array $params = []): void
    {
        if (!self::$projectPath) {
            throw new MicroFrameworkException('Project is not defined', 500);
        }

        self::initViewVariables();

        if (!is_null($controllerName)) {
            $isAdminPanel = false;

            if (str_starts_with($controllerName, 'pa')) {
                $isAdminPanel = true;
                $controllerName = lcfirst(substr($controllerName, 2));
            }

            self::loadController($controllerName, $controllerMethod, $controllerArguments, ['api' => $params['api'] ?? false, 'isAdminPanel' => $isAdminPanel]);
        }
    }

    /**
     * Get path
     * @param string $name controller / model / view
     * @return string|false
     */
    public static function getPath(string $name): string|false
    {
        if (!in_array($name, array_keys(self::$paths))) {
            return false;
        }

        return self::$paths[$name];
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
        if (isset($params['isAdminPanel']) && $params['isAdminPanel']) {
            if (!self::getConfig()->adminPanel) {
                throw new NotFoundException('Admin panel is disabled');
            } elseif (!self::getConfig()->authControl) {
                throw new NotFoundException('Account control is disabled');
            } elseif (!Account::isLogged()) {
                throw new NotFoundException('You not logged');
            } elseif (!Account::$account['account']['admin']) {
                throw new NotFoundException('You not have permission to admin panel');
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

        AuthControl::run($class, $method, isset($params['api']) && $params['api']);

        try {
            /** @var Controller $controller */
            $controller = new $class();
            $controller->name = $name;
            $controller->method = $method;
            $controller->arguments = $arguments;
            $controller->data = self::getData();
            $controller->htmlGenerator = new Html();
            $controller->params = $params;
            $controller->table = new Table();
            $controller->table->controller = $controller;
            $controller->table->data = $controller->data;

            if (!method_exists($controller, $method)) {
                throw new Exception('Method ' . $method . ' not exists in controller ' . $name);
            }
        } catch (NotFoundException $exception) {
            throw new NotFoundException($exception->getHiddenMessage());
        } catch (Exception $exception) {
            throw new MicroFrameworkException($exception->getMessage(), $exception->getCode());
        }

        call_user_func_array([$controller, $method], $arguments);

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

}