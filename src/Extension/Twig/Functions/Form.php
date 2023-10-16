<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use Twig\TwigFunction;

class Form
{

    public function __construct(&$environment, $controller)
    {
        $formFunction = new TwigFunction('form', function () use ($controller) {
            return new \Krzysztofzylka\MicroFramework\Extension\Form\Form($controller);
        });

        $environment->addFunction($formFunction);
    }

}