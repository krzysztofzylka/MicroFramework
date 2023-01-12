<?php

namespace Krzysztofzylka\MicroFramework\Exception;

class NotFoundException extends MicroFrameworkException {

    public function __construct(string $message = 'Object not found.') {
        parent::__construct($message, 404);
    }

}