<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use Krzysztofzylka\Arrays\Arrays;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Extension\Response;
use Krzysztofzylka\Request\Request;

/**
 * Route
 */
class Route
{

    /**
     * Starts the execution of a controller method.
     * @param string $controller The name of the controller.
     * @param string $method The name of the method to be executed.
     * @param array $parameters An optional array of parameters to be passed to the method.
     * @return Controller The controller object.
     * @throws NotFoundException When the specified method does not exist in the controller.
     */
    public function start(string $controller, string $method, array $parameters = []): Controller
    {
        DebugBar::timeStart('route_' . spl_object_hash($this), 'Route start');
        $class = $this->loadControllerClass($controller);
        DebugBar::timeStart('define_variables', 'Define controller variables');
        $class->name = $controller;
        $class->action = $method;
        $class->response = new Response();
        $class->data = Request::isPost() ? Arrays::escape($_POST) : null;
        DebugBar::timeStop('define_variables');

        if (!is_null($class->data)) {
            DebugBar::addFrameworkMessage($class->data, 'Post data');
        }

        if (!method_exists($class, $method) && !method_exists($class, '__call')) {
            throw new NotFoundException('Method not found');
        }


        DebugBar::timeStart('controller_' . spl_object_hash($class), 'Init controller');
        $class->$method(...$parameters);
        DebugBar::timeStop('controller_' . spl_object_hash($class));
        DebugBar::timeStop('route_' . spl_object_hash($this));

        DebugBar::addFrameworkMessage($class, 'Controller object');

        return $class;
    }

    /**
     * Loads the controller class based on the given controller name.
     * @param string $controller The name of the controller.
     * @return Controller The loaded controller object.
     * @throws NotFoundException When the specified controller or its namespace is not found or fails to instantiate.
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