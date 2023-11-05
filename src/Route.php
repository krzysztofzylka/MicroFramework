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
            $className = 'Krzysztofzylka\MicroFramework\Controllers\\' . $controller;

            try {
                /** @var Controller $class */
                $class = new $className();
            } catch (Exception) {
                $className = 'src\\Controller\\' . $controller;

                try {
                    /** @var Controller $class */
                    $class = new $className();
                } catch (Exception) {
                    throw new NotFoundException('Controller not found');
                }
            }

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

}