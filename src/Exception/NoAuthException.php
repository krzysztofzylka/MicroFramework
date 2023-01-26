<?php

namespace Krzysztofzylka\MicroFramework\Exception;

/**
 * Not found exception
 * @package Exception
 */
class NoAuthException extends MicroFrameworkException {

    public function __construct(string $message = 'Not authorized.') {
        parent::__construct($message, 401);
    }

}