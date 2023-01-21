<?php

namespace controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\ValidationException;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Validation\Validation;

class form extends Controller {

    public function validationTest() {

        $validation = new Validation();
        $validation->setValidation(
            [
                'validTest' => [
                    'text' => [
                        'required',
                        'length' => ['min' => 6, 'max' => 48],
                        function ($content) : void {
                            if ($content === 'testing') {
                                throw new ValidationException('content is testing');
                            }
                        }
                    ]
                ]
            ]
        );
        $validation->validate($this->data);

        $form1 = (new Html())->form(
            (new Html())
                ->input('validTest/text', 'required')
                ->button('Wyślij')
        );

        $this->loadView(null, ['form1' => $form1]);
    }

}