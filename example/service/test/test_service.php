<?php

namespace service\test;

use Krzysztofzylka\MicroFramework\Service;

class test_service extends Service {

    public function test() {
        return $this->loadModel('account')->findAll();
    }

}