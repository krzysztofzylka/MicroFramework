<?php

namespace Model;

use Krzysztofzylka\MicroFramework\Model;

class test extends Model {

    public bool $useTable = false;

    public function test() {
        return 'execute test method';
    }

}