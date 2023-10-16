<?php

namespace app\controller;

use Krzysztofzylka\MicroFramework\Controller;

class table extends Controller {

    public function table() {
        $this->table->setModel('example');
        $this->table->columns = [
            'example.id' => [
                'title' => 'ID'
            ],
            'example.name' => [
                'title' => 'Name'
            ],
            'example.status' => [
                'title' => 'Status'
            ]
        ];
        $this->table->actions = [
            [
                'value' => 'Test',
                'dialogbox' => false
            ],
            [
                'value' => 'Dialogbox',
                'href' => '/table/dialogbox',
                'type' => 'secondary'
            ]
        ];
        $this->loadView(['table' => $this->table->render()]);
    }

    public function dialogbox() {
        $this->layout = 'dialogbox';
        $this->title = 'Dialogbox';
        $this->loadView();
    }

}