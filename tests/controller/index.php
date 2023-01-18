<?php

namespace controller;

use Krzysztofzylka\MicroFramework\Controller;

class index extends Controller {

    public function index() {
        $this->loadView();
    }
}