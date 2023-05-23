<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use Twig\TwigFunction;

class Translate {

    public function __construct(&$environment) {
        $translationFunction = new TwigFunction('__', function (string $name) {
            return __($name);
        });
        $environment->addFunction($translationFunction);
    }

}