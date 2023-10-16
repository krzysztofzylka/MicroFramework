<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use krzysztofzylka\SimpleLibraries\Library\Debug;
use Twig\TwigFunction;

class DebugTable
{

    public function __construct(&$environment)
    {
        $formFunction = new TwigFunction('debugTable', function ($data) {
            Debug::print_r($data);
        });

        $environment->addFunction($formFunction);
    }

}