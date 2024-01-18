<?php

namespace src\Controller;

use krzysztofzylka\DatabaseManager\CreateTable;
use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Table\Cell;
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
//        (new CreateTable())
//            ->setName('test')
//            ->addIdColumn()
//            ->addSimpleVarcharColumn('a', 100)
//            ->addSimpleVarcharColumn('b')
//            ->addDateModifyColumn()
//            ->addDateCreatedColumn()
//            ->execute();

        $this->loadModel('test');

//        for ($i=0; $i<=600; $i++) {
//            $this->Test->setId(null)->save([
//                'a' => Generator::uniqId(50),
//                'b' => Generator::uniqId(50)
//            ]);
//        }

        $table = new Table();
        $table->addAction(
            'Generate new data',
            '/index/tableGenerate',
            'ajaxlink'
        );
        $table->addColumn('test.a', 'Test', null, ['width' => '200px']);
        $table->addColumn('test.b', 'Test2', function (Cell $cell) {
            return 'nowy tekst: ' . $cell->value;
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