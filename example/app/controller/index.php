<?php

namespace app\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Storage\Storage;

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
        $this->title = 'Test dialogbox';
        $this->dialogboxWidth = 500;
        $this->loadView();
    }

    public function table(): void
    {
        $this->table->model = $this->loadModel('account');
        $this->table->orderBy = 'account.id DESC';
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

    public function alert() {
        $this->responseAlert('alert', 'OK', '', ['pageReload' => true]);
    }

    public function alert2() {
        $this->responseAlert('alert', 'ERR');
    }

    public function testLog() {
        $this->log('test log');
        $this->responseAlert('Create log success');
    }

    public function modalSave() {
        $this->loadModel('example')->save($this->data);
    }

    public function storage() {
        $storage = new Storage();
        $storage->setFileName('testing.txt')->setDirectory('test');
        dumpe($storage->write('storage content'), $storage->read(), $storage->getModifiedDate(), $storage->delete());
    }

}