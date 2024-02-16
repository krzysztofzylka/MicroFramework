<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\CustomFunctions;

use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;
use Twig\Environment;

return new class() {

    public function load(Environment $twigEnvironment): void
    {
        $renderFunction = new \Twig\TwigFunction('render', function (string $action, array $variables = []) {
            $dir = Kernel::getPath('view');

            if (str_starts_with($action, '/')) {
                $dir = Kernel::getPath('project');
            }

            View::simpleLoad($dir . '/' . $action, $variables);
        });

        $twigEnvironment->addFunction($renderFunction);
    }

};