<?php

namespace Krzysztofzylka\MicroFramework\Exception;

class NotFoundException extends MicroFrameworkException {

    public function __construct() {
        parent::__construct('Object not found.', 404);
    }

}