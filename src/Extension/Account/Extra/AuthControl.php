<?php

namespace Krzysztofzylka\MicroFramework\Extension\Account\Extra;

use Exception;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\ControllerApi;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NoAuthException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Account\Enum\AuthControlAction;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\PHPDoc;

/**
 * Auth control
 * @package Extension
 */
class AuthControl
{

    /**
     * Start check authorization
     * @throws NotFoundException
     * @throws NoAuthException
     * @throws MicroFrameworkException
     */
    public static function run(string $class, string $method, bool $isApi): void
    {
        if (Kernel::getConfig()->authControl) {
            $checkAuthorization = self::checkAuthorization($class, $method);

            if (!$checkAuthorization) {
                if ($isApi) {
                    (new ControllerApi())->responseError('Not authorized', 401);
                } else {
                    switch (Kernel::getConfig()->authControlAction) {
                        case AuthControlAction::redirect:
                            (new Controller())->redirect(Kernel::getConfig()->authControlRedirect);
                        case AuthControlAction::exception:
                            throw new NoAuthException();
                    }
                }
            }
        }
    }

    /**
     * Check class method authorization
     * @param string $class class name
     * @param string $method method name
     * @return bool
     * @throws MicroFrameworkException
     */
    public static function checkAuthorization(string $class, string $method): bool
    {
        try {
            $requireAuth = PHPDoc::getClassMethodComment($class, $method, 'auth')[0] ?? Kernel::getConfig()->authControlDefaultRequireAuth;

            if (is_string($requireAuth)) {
                $requireAuth = filter_var($requireAuth, FILTER_VALIDATE_BOOLEAN);
            }

            if (!$requireAuth) {
                return true;
            }

            return Account::isLogged();
        } catch (Exception $exception) {
            throw new MicroFrameworkException($exception->getMessage());
        }
    }

}