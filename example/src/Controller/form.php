<?php

namespace src\Controller;

use Krzysztofzylka\MicroFramework\Controller;

class form extends Controller
{
    /**
    * @return void
    * @throws MicroFrameworkException
    * @throws NotFoundException
    */
    public function index(): void
    {
        $form = $this->loader->form($this);
        $form->input('form/input', 'Input');
        $form->select('form/select', 'Select', ['a' => '1', 'b' => '2']);
        $form->textarea('form/textarea', 'Textarea');
        $form->checkbox('form/checkbox', 'Checkbox');
        $form->fileSelect('form/fileSelect');
        $form->hiddenInput('form/hiddeninput', 'xxx');
        $form->simpleTextarea('form/simple_textarea');
        $form->submitButton('Submit');

        $this->loadView(
            variables: [
                'form' => $form
            ]
        );
    }

}