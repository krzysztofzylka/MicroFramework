<?php

namespace src\Controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;

class index extends Controller
{

    /**
     * @throws NotFoundException
     */
    public function index(): void
    {
        $this->loadModel('test');
        $this->set('variable', 'test');
        $this->loadView();
    }

}