<?php

namespace controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\ViewException;

class index extends Controller {

    /**
     * @return void
     * @throws ViewException
     */
    public function index() : void {
        $this->loadView();
    }

    public function dialogbox() {
        $this->layout = 'dialogbox';
    }
}