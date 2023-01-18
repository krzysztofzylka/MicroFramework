<?php

namespace Krzysztofzylka\MicroFramework\Api;

use Krzysztofzylka\MicroFramework\Extension\Account;

class Authorization {

    public function basic(string $username, string $password) {
        try {
            return (new Account())->login($username, $password);
        } catch (\Exception) {
            return false;
        }
    }

}