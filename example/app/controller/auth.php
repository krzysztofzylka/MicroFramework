<?php

namespace app\controller;

use Exception;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\ValidationException;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Extension\Validation\Validation;
use Krzysztofzylka\MicroFramework\Kernel;

class auth extends Controller {

    /**
     * @return void
     * @throws ViewException
     * @throws MicroFrameworkException
     */
    public function index() : void {
        if (Account::isLogged()) {
            $this->redirect($_ENV['config_default_page']);
        }

        $validation = new Validation();
        $validation->setValidation(
            [
                'auth' => [
                    'login' => [
                        'required',
                        function () {
                            if (empty($this->data['auth']['password'])) {
                                throw new ValidationException('Password is required');
                            }
                        },
                        function () {
                            try {
                                $account = new Account();
                                $account->login($this->data['auth']['login'], $this->data['auth']['password']);

                                $this->redirect($_ENV['config_default_page']);
                            } catch (Exception) {
                                throw new ValidationException('Login failed');
                            }
                        }
                    ],
                    'password' => [
                        'required'
                    ]
                ]
            ]
        );
        $validationData = $validation->validate($this->data);

        if ($this->data && !$validationData) {
            var_dump($this->data);
        }

        $form = (new Html())->form(
            (new Html())
                ->setFormValidation($validationData)
                ->input('auth/login', 'Login')
                ->input('auth/password', 'Password', ['type' => 'password'])
                ->button('Zaloguj')
        );

        $this->loadView(['form' => $form]);
    }

    public function register() : void {
        if (Account::isLogged()) {
            $this->redirect($_ENV['config_default_page']);
        }

        $validation = new Validation();
        $validation->setValidation(
            [
                'auth' => [
                    'email' => [
                        'required',
                        'isEmail',
                        function () {
                            if (empty($this->data['auth']['password'])) {
                                throw new ValidationException('Password is required');
                            }
                        },
                        function () {
                            try {
                                $account = new Account();
                                $account->registerUser(null, $this->data['auth']['password'], $this->data['auth']['email']);

                                $this->redirect($_ENV['config_default_page']);
                            } catch (Exception $exception) {
                                throw new ValidationException($exception->getMessage());
                            }
                        }
                    ],
                    'password' => [
                        'required',
                        'length' => ['min' => 6]
                    ]
                ]
            ]
        );
        $validationData = $validation->validate($this->data);

        if ($this->data && !$validationData) {
            var_dump($this->data);
        }

        $form = (new Html())->form(
            (new Html())
                ->setFormValidation($validationData)
                ->input('auth/email', 'E-Mail')
                ->input('auth/password', 'Password', ['type' => 'password'])
                ->button('Register')
        );

        $this->loadView(['form' => $form]);
    }

}