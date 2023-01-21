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
                    ],
                    'text2' => [
                        'required'
                    ]
                ]
            ]
        );
        $validationData = $validation->validate($this->data);

        $form1 = (new Html())->form(
            (new Html())
                ->setFormValidation($validationData)
                ->input('validTest/text', 'required, length 6-48, no "testing" value')
                ->input('validTest/text2', 'required')
                ->button('Wyślij')
        );

        $this->loadView(null, ['form1' => $form1]);
    }

}