<?php

namespace Controller;

use Krzysztofzylka\MicroFramework\controller;

class test extends Controller {

    public function index($variable) {
        echo 'first method!';
        echo $variable;
        echo '<hr />';
        echo '<pre>';
        var_dump($this);
        var_dump($this->loadModel('test'));
        var_dump($this->Test->test(), $this->name);
    }

    public function view() {
        $this->loadView('view');
    }

    public function insert() {
        $this->loadModel('table');

        try {
            $this->Table->insert([
                'username' => 'asd',
                'password' => 'pass',
                'email' => 'adres@email.pl'
            ]);

            $this->Table->updateValue('password', 'new password');

            $this->Table->update([
                'username' => 'new username',
                'email' => 'new@email.com'
            ]);

            var_dump($this->Table->getId());

            var_dump($this->Table->findCount());

//            var_dump($this->Table->findAll());
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

}