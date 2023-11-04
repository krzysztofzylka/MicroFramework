<?php

namespace Krzysztofzylka\MicroFramework\Exception;

use Exception;

/**
 * Not found exception
 */
class NotFoundException extends Exception
{

    public function __construct(string $message = "")
    {
        parent::__construct($message, 404);
    }

}