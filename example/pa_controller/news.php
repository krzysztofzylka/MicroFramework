<?php

namespace pa_controller;

use Krzysztofzylka\MicroFramework\Controller;

class news extends Controller {

    public function index() {
        $this->loadView();
    }

}