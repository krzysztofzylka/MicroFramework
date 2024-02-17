<?php

namespace src\Controller;

use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Table\Cell;

class index extends Controller
{

    /**
     * @throws HiddenException
     * @throws NotFoundException
     * @throws MicroFrameworkException
     */
    public function index(): void
    {
        $this->dialogboxTitle = 'Test dialogbox';
        $this->loadModel('test');
        $this->set('variable', 'Test variable');
        $this->set('time', time());
        $this->loadView();
    }

    public function response(): void
    {
        $this->redirect('/index/index');
    }

    public function toast(): void
    {
        $this->response->toast('test');
    }

    public function toastreload(): void
    {
        $this->response->toast('test', dialogboxReload: true);
    }

    public function form(): void
    {
        $this->dialogboxTitle = 'Form';
        $this->dialogboxWidth = 400;

        if ($this->data) {
            var_dump($this->data);

            if ($this->data['test'] === 'close') {
                $this->response->toast('success', dialogboxClose: true);
            }
        }

        $this->loadView();
    }

    public function table(): void
    {
        $this->loadModel('test');

        $table = $this->loader->table();
        $table->addAction(
            'Generate new data',
            '/index/tableGenerate',
            'ajaxlink'
        );
        $table->addColumn('test.a', 'Test', null, ['width' => '200px']);
        $table->addColumn('test.b', 'Test2', function (Cell $cell) {
            return $cell->html->link($cell->value, '#');
        });
        $table->addColumn('badge', 'Badge', function (Cell $cell) {
            return $cell->html->badge('badge test', 'red', true);
        }, ['width' => '200px'], textAlign: 'center');
        $table->addColumn('progress', 'Progress', function (Cell $cell) {
            return $cell->html->progressbar(82, 'yellow');
        }, textAlign: 'right');
        $table->addColumn('textcolor', 'Text color', function (Cell $cell) {
            return $cell->html->textColor('color', 'green');
        });
        $table->setModel($this->Test);
        echo $table;
    }

    public function tableGenerate()
    {
        $this->loadModel('test')->save([
            'a' => Generator::uniqId(50),
            'b' => Generator::uniqId(50)
        ]);

        $this->response->toast('success', reloadPage: true);
    }

}