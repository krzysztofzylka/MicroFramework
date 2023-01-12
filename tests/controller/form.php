<?php

namespace Controller;

use Krzysztofzylka\MicroFramework\controller;

class form extends Controller {

    public function test() {
        if ($this->data) {
            var_dump($this->data);
        }

        $this->loadView();
    }

}