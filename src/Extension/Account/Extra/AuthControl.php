<?php

namespace Krzysztofzylka\MicroFramework\Extension\Account\Extra;

use Exception;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\PHPDoc;

/**
 * Auth control
 * @package Extension
 */
class AuthControl {

    /**
     * Check class method authorization
     * @param $class
     * @param $method
     * @return bool
     */
    public static function checkAuthorization($class, $method) : bool {
        try {
            $requireAuth = PHPDoc::getClassMethodComment($class, $method, 'auth')[0] ?? Kernel::getConfig()->authControlDefaultRequireAuth;

            if(is_string($requireAuth)) {
                $requireAuth = filter_var($requireAuth, FILTER_VALIDATE_BOOLEAN);
            }
        } catch (Exception) {
            return false;
        }

        if (!$requireAuth) {
            return true;
        }

        return Account::isLogged();
    }

}