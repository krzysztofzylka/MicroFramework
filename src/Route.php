<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Response;
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
        try {
            DebugBar::timeStart('route', 'Route start');
            $class = $this->loadControllerClass($controller);
            DebugBar::timeStart('define_variables', 'Define controller variables');
            $class->name = $controller;
            $class->action = $method;
            $class->response = new Response();
            DebugBar::timeStop('define_variables');

            if (!method_exists($class, $method)) {
                throw new NotFoundException('Method not found');
            }

            $class->$method(...$parameters);
            DebugBar::timeStop('route');

            DebugBar::addFrameworkMessage($class, 'Controller object');

            return $class;
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Load controller class
     * @param string $controller
     * @return Controller
     * @throws NotFoundException
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
        } catch (Exception) {
            throw new NotFoundException('Controller not found');
        }
    }

}