<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Extension\Response;
use krzysztofzylka\SimpleLibraries\Library\Request;
use Throwable;

/**
 * Route
 */
class Route
{

    /**
     * Start route
     * @param string $controller controller name
     * @param string $method method name
     * @param array $parameters additional parameters
     * @return Controller
     * @throws NotFoundException
     * @throws Throwable
     */
    public function start(string $controller, string $method, array $parameters = []): Controller
    {
        DebugBar::timeStart('route_' . spl_object_hash($this), 'Route start');
        $class = $this->loadControllerClass($controller);
        DebugBar::timeStart('define_variables', 'Define controller variables');
        $class->name = $controller;
        $class->action = $method;
        $class->response = new Response();
        $class->data = Request::isPost() ? (new Request())->getAllPostEscapeData() : null;
        DebugBar::timeStop('define_variables');

        if (!is_null($class->data)) {
            DebugBar::addFrameworkMessage($class->data, 'Post data');
        }

        if (!method_exists($class, $method)) {
            throw new NotFoundException('Method not found');
        }

        $class->$method(...$parameters);
        DebugBar::timeStop('route_' . spl_object_hash($this));

        DebugBar::addFrameworkMessage($class, 'Controller object');

        return $class;
    }

    /**
     * Load controller class
     * @param string $controller
     * @return Controller
     * @throws NotFoundException
     * @throws Exception
     */
    private function loadControllerClass(string $controller): Controller
    {
        $className = 'Krzysztofzylka\MicroFramework\Controllers\\' . $controller;

        try {
            return new $className();
        } catch (Exception) {
        }

        $className = 'src\\Controller\\' . $controller;

        try {
            return new $className();
        } catch (Exception $exception) {
            Log::log('Fail load controller', 'ERROR', ['exception' => $exception->getMessage()]);

            throw new NotFoundException('Controller not found');
        }
    }

}