<?php

namespace app;

use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Exception\ViewException;

class Controller extends \Krzysztofzylka\MicroFramework\Controller {

    /**
     * Load view
     * @param array $variables
     * @param ?string $name
     * @return void
     * @throws ViewException
     * @throws NotFoundException
     */
    public function loadView(array $variables = [], ?string $name = null): void
    {
        $view = new \app\View();
        $view->setController($this);
        $this->viewLoaded = true;

        echo $view->render(array_merge($this->viewVariables, $variables), $name ?? $this->method);
    }

}