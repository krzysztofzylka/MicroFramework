<?php

namespace Controller;

use Krzysztofzylka\MicroFramework\controller;

class test extends Controller {

    public function index($variable) {
        echo 'first method!';
        echo $variable;
    }

}