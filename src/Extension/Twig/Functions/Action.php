<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use Krzysztofzylka\MicroFramework\Debug;
use Krzysztofzylka\MicroFramework\Kernel;
use Twig\TwigFunction;

class Action
{

    public function __construct(&$environment)
    {
        $formFunction = new TwigFunction('action', function ($name, $type = false) {
            if ($_ENV['config_debug']) {
                $time_start = microtime(true);
            }

            $explode = explode('/', $name);

            if (empty($explode[0])) {
                unset($explode[0]);
                $explode = array_values($explode);
            }

            $controller = $explode[0];
            $method = $explode[1];

            ob_start();
            $arguments = array_slice($explode, 2);
            $controller = Kernel::loadController($controller, $method, $arguments);

            if ($type === 'table') {
                if (!$controller->table->isRender) {
                    echo $controller->table->render();
                }
            }

            if ($_ENV['config_debug']) {
                Debug::$data['times']['twig_action_' . $name . '_' . $type . '_' . random_int(0, 99999)] = microtime(true) - $time_start;
            }

            return ob_get_clean();
        });

        $environment->addFunction($formFunction);
    }

}