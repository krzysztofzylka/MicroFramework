<?php

namespace src\Controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;

class index extends Controller
{

    /**
     * @throws NotFoundException
     * @throws HiddenException
     */
    public function index(): void
    {
        DebugBar::timeStart('controller');
        $this->loadModel('test');
        $this->set('variable', 'Test variable');
        DebugBar::addMessage($this->Test->findAll(), 'Find');
        $this->loadView();
        DebugBar::timeStop('controller');
    }

}