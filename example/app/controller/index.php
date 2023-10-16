<?php

namespace app\controller;

use krzysztofzylka\DatabaseManager\Exception\InsertException;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\CommonFile\CommonFile;
use Krzysztofzylka\MicroFramework\Extension\Storage\Storage;
use Krzysztofzylka\MicroFramework\Service;

class index extends Controller {

    /**
     * @return void
     * @throws NotFoundException
     * @throws InsertException
     */
    public function index(): void
    {
    }

    public function dialogbox(): void
    {
        $this->layout = 'dialogbox';
        $this->title = 'Test dialogbox';
        $this->dialogboxWidth = 500;
    }

    public function table(): void
    {
    }

    public function tableRender(): void
    {
        $this->layout = 'table';
        $this->table->isAjax = true;
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
        $this->table->paginationLimit = 5;
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

    public function table3(): void
    {
    }

    public function table3Render(): void
    {
        $this->layout = 'table';
        $this->table->isAjax = true;
        $this->table->model = $this->loadModel('example');
        $this->table->columns = [
            'example.id' => [
                'title' => 'ID',
                'width' => 100
            ],
            'example.name' => [
                'title' => 'Name',
                'wordBreak' => true
            ],
            'example.status' => [
                'title' => 'Status'
            ],
            'example.date_modify' => [
                'title' => 'Modify',
                'noWrap' => true
            ]
        ];
        $this->table->paginationLimit = 5;
    }

    public function alert() {
        $this->responseAlert('alert', 'OK', '', ['pageReload' => true]);
    }

    public function alertRedirect() {
        $this->responseAlert('Test alert', 'OK', '', ['redirect' => '/index/table']);
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
        $storage->setFileName('testing.txt')->setDirectory('test')->setAccountIsolator();
        dumpe($storage->write('storage content'), $storage->read(), $storage->getModifiedDate(), $storage->delete(), $storage);
    }

    public function confirm(): void {
        if ($this->confirmAction()) {
            $this->responseAlert('confirm', 'OK', '', ['dialog' => ['close' => true]]);
        }
    }

    public function dialogboxClose(): void {
        $this->responseAlert('Zamknieto dialogbox i przeładowano stronę główną.', 'OK', '', ['pageReload' => true, 'dialog' => ['close' => true]]);
    }

    public function service(): void {
        dump(Service::loadService('test_service')->test());
        exit;
    }

}