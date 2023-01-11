<?php

namespace Controller;

use Krzysztofzylka\MicroFramework\controller;

class test extends Controller {

    public function index($variable) {
        echo 'first method!';
        echo $variable;
        echo '<hr />';
        echo '<pre>';
        var_dump($this);
        var_dump($this->loadModel('test'));
        var_dump($this->Test->test(), $this->name);
    }

    public function view() {
        $this->loadView('view');
    }

}