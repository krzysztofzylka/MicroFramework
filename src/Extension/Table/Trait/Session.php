<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Trait;

trait Session
{


    /**
     * Get saved session
     * @return mixed
     */
    private function getSession(): mixed
    {
        if (!$this->session) {
            $this->session = \krzysztofzylka\SimpleLibraries\Library\Session::get('table_' . $this->id . '_parameters');
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
        \krzysztofzylka\SimpleLibraries\Library\Session::set('table_' . $this->id . '_parameters', $data);
    }

}