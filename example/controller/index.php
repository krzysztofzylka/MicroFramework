<?php

namespace controller;

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
}