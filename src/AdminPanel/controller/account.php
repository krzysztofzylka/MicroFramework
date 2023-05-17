<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Table\Extra\Cell;

class account extends Controller
{

    public function index(): void
    {
        $this->table->model = $this->loadModel('account');
        $this->table->columns = [
            'account.id' => [
                'title' => 'ID',
                'width' => 50
            ],
            'account.username' => [
                'title' => 'Username'
            ],
            'account.admin' => [
                'title' => 'Is admin',
                'width' => 80,
                'value' => function (Cell $cell) {
                    return $cell->val ? 'Yes' : 'No';
                }
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

    public function logout(): void
    {
        $account = new \Krzysztofzylka\MicroFramework\Extension\Account\Account();
        $account->logout();
        $this->redirect('/' . $_ENV['config_default_page']);
    }

}