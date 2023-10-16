<?php

namespace api\controller;

use Krzysztofzylka\MicroFramework\ControllerApi;

class auth extends ControllerApi {

    public function test(): void
    {
        $this->response->json(['status' => 'OK']);
    }

}