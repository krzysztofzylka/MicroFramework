<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
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
            $className = 'src\\Controller\\' . $controller;

            try {
                /** @var Controller $class */
                $class = new $className();
            } catch (\Exception $exception) {
                throw new NotFoundException('Controller not found');
            }

            $class->name = $controller;
            $class->action = $method;

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