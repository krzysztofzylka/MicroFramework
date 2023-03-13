<?php

namespace app\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\ViewException;

class index extends Controller {

    /**
     * @return void
     * @throws ViewException
     */
    public function index(): void
    {
        $this->loadView();
    }

    public function dialogbox(): void
    {
        $this->layout = 'dialogbox';
        echo 'xdd';
    }

    public function table(): void
    {
        $this->table->model = $this->loadModel('account');
        $this->table->columns = [
            'account.username' => [
                'title' => 'Username'
            ],
            'account.password' => [
                'title' => 'Password',
                'value' => 'asd'
            ],
            'account.date_created' => [
                'title' => 'Created',
                'value' => function ($cell) {
                    return 'xx - ' . $cell->val;
                }
            ],
            'account.date_modify' => [
                'title' => 'Modify'
            ]
        ];

        $this->loadView(['table' => $this->table->render()]);
    }

    public function table2(): void
    {
        $this->table->model = $this->loadModel('example');
        $this->table->columns = [
            'example.name' => [
                'title' => 'Name'
            ],
            'example.status' => [
                'title' => 'Status'
            ],
            'example.date_created' => [
                'title' => 'Created',
                'value' => function ($cell) {
                    return 'Modify - ' . $cell->val;
                }
            ],
            'example.date_modify' => [
                'title' => 'Modify'
            ]
        ];

        $this->loadView(['table' => $this->table->render()], 'table');
    }
}