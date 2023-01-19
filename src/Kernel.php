<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Exception\ConnectException;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extra\ObjectNameGenerator;
use krzysztofzylka\SimpleLibraries\Library\File;
use krzysztofzylka\SimpleLibraries\Library\Request;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Kernel
 */
class Kernel {

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
        'controller' => null,
        'api_controller' => null,
        'model' => null,
        'view' => null,
        'storage' => null,
        'logs' => null
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
    public static function create(string $projectPath) : void {
        self::$projectPath = $projectPath;
        self::$paths['controller'] = $projectPath . '/controller';
        self::$paths['api_controller'] = $projectPath . '/api_controller';
        self::$paths['model'] = $projectPath . '/model';
        self::$paths['view'] = $projectPath . '/view';
        self::$paths['storage'] = $projectPath . '/storage';
        self::$paths['logs'] = self::$paths['storage'] . '/logs';

        foreach (self::$paths as $name => $path) {
            self::$paths[$name] = File::repairPath($path);

            File::mkdir($path);
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
    public static function init(?string $controllerName = null, string $controllerMethod = 'index', array $controllerArguments = [], array $params = []) : void {
        if (!self::$projectPath) {
            throw new MicroFrameworkException('Project is not defined', 500);
        }

        View::$filesystemLoader = new FilesystemLoader(self::getPath('view'));
        View::$environment = new Environment(View::$filesystemLoader, ['debug' => true]);
        View::$environment->addExtension(new DebugExtension());

        if (!is_null($controllerName)) {
            self::loadController($controllerName, $controllerMethod, $controllerArguments, ['api' => $params['api'] ?? false]);
        }
    }

    /**
     * Run framework
     * @return void
     * @throws ConnectException
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function run() : void {
        if (!isset(self::$config)) {
            self::$config = new ConfigDefault();
        }

        if (self::$config->database) {
            $databaseManager = new DatabaseManager();
            $databaseManager->connect(
                (new DatabaseConnect())
                    ->setHost(self::$config->databaseHost)
                    ->setUsername(self::$config->databaseUsername)
                    ->setPassword(self::$config->databasePassword)
                    ->setDatabaseName(self::$config->databaseName)
            );
        }

        $url = $_GET['url'] ?? self::getConfig()->defaultPage;
        $explode = explode('/', $url);

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
     * Get project path
     * @return string
     */
    public static function getProjectPath() : string {
        return self::$projectPath;
    }

    /**
     * Get path
     * @param string $name controller / model / view
     * @return string|false
     */
    public static function getPath(string $name) : string|false {
        if (!in_array($name, array_keys(self::$paths))) {
            return false;
        }

        return self::$paths[$name];
    }

    /**
     * Load controller
     * @param string $name
     * @param string $method
     * @param array $arguments
     * @param array $params additional params for loader
     * @return Controller
     * @throws NotFoundException
     */
    public static function loadController(string $name, string $method = 'index', array $arguments = [], array $params = []) : Controller {
        if (isset($params['api']) && $params['api']) {
            $class = ObjectNameGenerator::controllerApi($name);
        } else {
            $class = ObjectNameGenerator::controller($name);
        }

        try {
            /** @var Controller $controller */
            $controller = new $class();
            $controller->name = $name;
            $controller->method = $method;
            $controller->arguments = $arguments;
            $controller->data = self::getData();
            $controller->htmlGenerator = new Html();

            if (!method_exists($controller, $method)) {
                throw new Exception();
            }
        } catch (Exception) {
            throw new NotFoundException();
        }

        call_user_func_array([$controller, $method], $arguments);

        return $controller;
    }

    /**
     * Autoload
     * @return void
     * @throws NotFoundException
     */
    public static function autoload() : void {
        spl_autoload_register(function ($class_name) {
            $path = File::repairPath(self::getProjectPath() . DIRECTORY_SEPARATOR . $class_name . '.php');

            if (!file_exists($path)) {
                throw new NotFoundException();
            }

            include($path);
        });
    }

    /**
     * Database connect
     * @param DatabaseConnect $databaseConnect
     * @return void
     * @throws DatabaseException
     */
    public static function databaseConnect(DatabaseConnect $databaseConnect) : void {
        try {
            $databaseManager = new DatabaseManager();
            $databaseManager->connect($databaseConnect);
        } catch (ConnectException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Get post data
     * @return ?array
     */
    public static function getData() : ?array {
        if (!Request::isPost()) {
            return null;
        }

        return Request::getAllPostEscapeData();
    }

    /**
     * Set config
     * @param object $config
     * @return void
     */
    public static function setConfig(object $config) : void {
        self::$config = $config;
    }

    /**
     * Get config
     * @return ConfigDefault
     */
    public static function getConfig() : object {
        return self::$config;
    }

}