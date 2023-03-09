<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\controller;

use Krzysztofzylka\MicroFramework\Controller;

class account extends Controller
{

    public function index()
    {
        $this->table->model = $this->loadModel('paAccount');
        $this->table->columns = [
            'account.id' => [
                'title' => 'ID',
                'width' => 50
            ],
            'account.username' => [
                'title' => 'Username'
            ],
            'account.date_created' => [
                'title' => 'Created',
                'width' => 250
            ],
            'account.date_modify' => [
                'title' => 'Modify',
                'width' => 250
            ]
        ];

        $this->loadView(['table' => $this->table->render()]);
    }

}