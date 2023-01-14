<?php

namespace Krzysztofzylka\MicroFramework;

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
        'model' => null,
        'view' => null
    ];

    /**
     * Init project
     * @param string $projectPath
     * @return void
     */
    public static function create(string $projectPath) : void {
        self::$projectPath = $projectPath;
        self::$paths['controller'] = $projectPath . '/controller';
        self::$paths['model'] = $projectPath . '/model';
        self::$paths['view'] = $projectPath . '/view';

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
     * @return void
     * @throws MicroFrameworkException
     * @throws NotFoundException
     */
    public static function init(?string $controllerName = null, string $controllerMethod = 'index', array $controllerArguments = []) : void {
        if (!self::$projectPath) {
            throw new MicroFrameworkException('Project is not defined', 500);
        }

        View::$filesystemLoader = new \Twig\Loader\FilesystemLoader(self::getPath('view'));
        View::$environment = new \Twig\Environment(View::$filesystemLoader, ['debug' => true]);
        View::$environment->addExtension(new \Twig\Extension\DebugExtension());

        if (!is_null($controllerName)) {
            self::loadController($controllerName, $controllerMethod, $controllerArguments);
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
     * @return Controller
     * @throws NotFoundException
     */
    public static function loadController(string $name, string $method = 'index', array $arguments = []) : Controller {
        $class = ObjectNameGenerator::controller($name);

        try {
            /** @var Controller $controller */
            $controller = new $class();
            $controller->name = $name;
            $controller->method = $method;
            $controller->arguments = $arguments;
            $controller->data = self::getData();
            $controller->htmlGenerator = new Html();

            if (!method_exists($controller, $method)) {
                throw new \Exception();
            }
        } catch (\Exception) {
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

}