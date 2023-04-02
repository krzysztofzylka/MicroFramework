<?php

namespace api\controller;

use Krzysztofzylka\MicroFramework\ControllerApi;

class auth extends ControllerApi {

    public function test() {
        $this->responseJson(['status' => 'OK']);
    }

}