<?php

namespace api_controller;

use Krzysztofzylka\MicroFramework\ControllerApi;

class test extends ControllerApi {

    public function index() {
        $this->responseJson([$this->getRequestMethod(), $this->getBodyContent()]);
    }
}