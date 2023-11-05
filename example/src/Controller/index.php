<?php

namespace src\Controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;

class index extends Controller
{

    /**
     * @throws NotFoundException
     * @throws HiddenException
     */
    public function index(): void
    {
        $this->loadModel('test');
        $this->set('variable', 'test');
        $this->Test->find();
        $this->loadView();
    }

}