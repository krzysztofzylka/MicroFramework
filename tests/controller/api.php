<?php

namespace Controller;

use Krzysztofzylka\MicroFramework\controller;
use Krzysztofzylka\MicroFramework\Trait\ApiController;

class api extends Controller {

    use ApiController;

    public function test(... $vars) {
        var_dump($vars);
    }

}