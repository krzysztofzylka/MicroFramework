<?php

namespace Krzysztofzylka\MicroFramework\Extension;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Form\Form;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;

class Loader
{

    /**
     * Load table
     * @return Table
     */
    public function table(): Table
    {
        return new Table();
    }

    /**
     * Load form
     * @param Controller $controller
     * @param array|null $validations
     * @param array|null $validationData
     * @return Form
     * @throws \Exception
     */
    public function form(Controller $controller, ?array $validations = null, ?array $validationData = null): Form
    {
        return new Form($controller, $validations, $validationData);
    }

}