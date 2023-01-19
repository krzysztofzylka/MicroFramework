<?php

namespace api_controller;

use Krzysztofzylka\MicroFramework\ControllerApi;

class test extends ControllerApi {

    public bool $auth = true;

    public function index() {
        $this->responseJson([$this->getRequestMethod(), $this->getBodyContent(), $_SERVER, $_SESSION, $_POST, $_GET, $_REQUEST, $_COOKIE]);
    }
}