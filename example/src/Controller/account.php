<?php

namespace src\Controller;

use Krzysztofzylka\MicroFramework\Controller;

class account extends Controller
{
    /**
    * @return void
    * @throws MicroFrameworkException
    * @throws NotFoundException
    */
    public function register(): void
    {
        if ($this->data) {
            $register = \Krzysztofzylka\MicroFramework\Extension\Authorization\Account::register($this->data['account']['email'], $this->data['account']['password']);

            if ($register) {
                $this->response->toast('Poprawnie zarejestrowano użytkownika');
            } else {
                $this->response->toast('Błąd rejestracji', '', 'ERR');
            }
        }

        $this->loadView();
    }

    /**
    * @return void
    * @throws MicroFrameworkException
    * @throws NotFoundException
    */
    public function login(): void
    {
        if ($this->data) {
            $register = \Krzysztofzylka\MicroFramework\Extension\Authorization\Account::login($this->data['account']['email'], $this->data['account']['password']);

            if ($register) {
                $this->response->toast('Poprawnie zalogowano');
            } else {
                $this->response->toast('Błąd logowania', '', 'ERR');
            }
        }

        $this->loadView();
    }

}