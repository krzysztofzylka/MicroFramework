<?php

namespace app\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\ValidationException;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Validation\Validation;

class form extends Controller {

    public function validationTest() {
        $validation = $this->loadModel('form')->validate();

        if ($this->data && $validation) {
            echo 'OK :)';
        }

        $form1 = (new Html())->form(
            (new Html())
                ->validateModel($this->Form)
                ->input('validTest/text', 'required, length 6-48, no "testing" value')
                ->input('validTest/text2', 'required')
                ->textarea('validTest/textarea', 'required, min length 10')
                ->select('validTest/select', ['' => '', 'a' => '1', 'b' => '2'])
                ->select('validTest/select2', ['' => '', 1 => 'a', 2 => 'b', 3 => 'c'], 2, 'Select 2')
                ->input('validTest/date', 'isDate', ['type' => 'date'])
                ->input('validTest/date2', 'isDate')
                ->input('validTest/email', 'isEmail')
                ->button('Check')
        );

        $this->loadView(['form1' => $form1]);
    }

}