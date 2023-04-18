<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Trait;

use Krzysztofzylka\MicroFramework\Extension\Account\Account;

/**
 * Session
 * @package Extension\Table\Trait
 */
trait Session
{


    /**
     * Get saved session
     * @return mixed
     */
    private function getSession(): mixed
    {
        if (!$this->session) {
            $this->session = \krzysztofzylka\SimpleLibraries\Library\Session::get($this->getSessionName());

            if (!$this->session && isset(Account::$accountRememberField)) {
                $this->session = json_decode(Account::$accountRememberField->get($this->getSessionName()), true);
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
        \krzysztofzylka\SimpleLibraries\Library\Session::set($this->getSessionName(), $data);

        if (isset(Account::$accountRememberField)) {
            Account::$accountRememberField->set($this->getSessionName(), json_encode($data));
        }
    }

    /**
     * Get session name
     * @return string
     */
    private function getSessionName(): string
    {
        return 'Session::Table::' . ($this->controller->params['admin_panel'] ? '1' : '0') . '::' . $this->controller->name . '::' . $this->controller->method . '::' . $this->id;
    }

}