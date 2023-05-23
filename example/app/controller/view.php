<?php

namespace app\controller;

use Krzysztofzylka\MicroFramework\Controller;

class view extends Controller
{

    public function renderTest(){
    }

    public function text() : void {
        $this->layout = 'none';

        echo 'test text';
    }

    public function table() : void {
        $this->layout = 'none';

        $this->table->columns = [
            '1' => [
                'title' => 'test'
            ]
        ];
    }

}