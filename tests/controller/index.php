<?php

namespace controller;

use Krzysztofzylka\MicroFramework\Controller;

class Index extends Controller {

    public function index() {
        $this->loadView();
    }

}