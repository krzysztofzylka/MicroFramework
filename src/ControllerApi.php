<?php

namespace Krzysztofzylka\MicroFramework;

class ControllerApi extends Controller {

    /**
     * Is API controller
     * @var bool
     */
    public bool $isApi = true;

    public function responseJson(array $data) {

    }

}