<?php

namespace src\Controller;

use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;

class index extends Controller
{

    /**
     * @throws HiddenException
     * @throws NotFoundException
     * @throws MicroFrameworkException
     */
    public function index(): void
    {
        DebugBar::timeStart('controller');
        $this->dialogboxTitle = 'Test dialogbox';
        $this->loadModel('test');
        $this->set('variable', 'Test variable');
        DebugBar::addMessage($this->Test->findAll(), 'Find');
        $this->set('time', time());
        $this->loadView();
        DebugBar::timeStop('controller');
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
        $table = new Table();
        $table->addColumn('a', 'Test');
        $table->addColumn('b', 'Test2');
        $data = [];

        for ($i=0; $i<=50; $i++) {
            $data[] = [
                'a' => 'a' . $i,
                'b' => 'b' . $i
            ];
        }

        $table->setData($data);

        echo $table;
    }

}