<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use Krzysztofzylka\MicroFramework\Kernel;
use Twig\TwigFunction;

class Action {

    public function __construct(&$environment) {
        $formFunction = new TwigFunction('action', function ($name, $type = false) {
            $explode = explode('/', $name);

            if (empty($explode[0])) {
                unset($explode[0]);
                $explode = array_values($explode);
            }

            $controller = $explode[0];
            $method = $explode[1];

            ob_start();
            $controller = Kernel::loadController($controller, $method);

            if ($type === 'table') {
                echo $controller->table->render();
            }

            return ob_get_clean();
        });

        $environment->addFunction($formFunction);
    }

}