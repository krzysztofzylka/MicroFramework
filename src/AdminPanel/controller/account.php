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
                'title' => __('micro-framework.admin_panel.accounts.username')
            ],
            'account.admin' => [
                'title' => __('micro-framework.admin_panel.accounts.is_admin'),
                'width' => 80,
                'value' => function (Cell $cell) {
                    return $cell->val ? __('micro-framework.yes') : __('micro-framework.no');
                }
            ],
            'account.date_created' => [
                'title' => __('micro-framework.admin_panel.accounts.created'),
                'width' => 250
            ],
            'account.date_modify' => [
                'title' => __('micro-framework.admin_panel.accounts.modify'),
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