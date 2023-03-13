<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Trait;

use Krzysztofzylka\MicroFramework\Extension\Account\Account;

trait Session
{


    /**
     * Get saved session
     * @return mixed
     */
    private function getSession(): mixed
    {
        $name = 'Session::Table::' . $this->id;

        if (!$this->session) {
            $this->session = \krzysztofzylka\SimpleLibraries\Library\Session::get($name);

            if (!$this->session && isset(Account::$accountRememberField)) {
                $this->session = json_decode(Account::$accountRememberField->get($name), true);
            }
        }

        return $this->session;
    }

    /**
     * Save session data
     * @param array $data
     * @return void
     */
    private function saveSession(array $data): void
    {
        $name = 'Session::Table::' . $this->id;

        \krzysztofzylka\SimpleLibraries\Library\Session::set($name, $data);

        if (isset(Account::$accountRememberField)) {
            Account::$accountRememberField->set($name, json_encode($data));
        }
    }

}