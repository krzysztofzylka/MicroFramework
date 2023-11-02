<?php

namespace api\controller;

use Krzysztofzylka\MicroFramework\ControllerApi;

class index extends ControllerApi {

    public bool $auth = false;

    /**
     * @return void
     * @allowMethod POST
     */
    public function index() {
        $this->response->json(['a']);
    }

}