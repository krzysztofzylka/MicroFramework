<?php

namespace api_controller;

use Krzysztofzylka\MicroFramework\ControllerApi;

class test extends ControllerApi {

    public function index() {
        $this->responseError('safdasf');
        $this->responseJson(['xdd' => 'xd^2']);
    }
}