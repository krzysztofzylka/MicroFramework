<?php

namespace src\Controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\XmlViewGenerator\XmlViewGenerator;

class index extends Controller
{

    /**
     * @throws NotFoundException
     * @throws HiddenException
     */
    public function index(): void
    {
        $xml = new XmlViewGenerator();
        $xml->loadXmlFile(__DIR__ . '/file.xml');
        $xml->loadNodeDescriptionsFromXSD(__DIR__ . '/schemat.xsd');

        die($xml->render());
        DebugBar::timeStart('controller');
        $this->loadModel('test');
        $this->set('variable', 'Test variable');
        DebugBar::addMessage($this->Test->findAll(), 'Find');
        $this->loadView();
        DebugBar::timeStop('controller');
    }

}