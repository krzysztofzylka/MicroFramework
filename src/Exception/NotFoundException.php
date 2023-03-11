<?php

namespace Krzysztofzylka\MicroFramework\Exception;

/**
 * Not found exception
 * @package Exception
 */
class NotFoundException extends MicroFrameworkException
{

    public function __construct(string $message = 'Object not found.')
    {
        $this->setHiddenMessage($message);

        parent::__construct('Object not found.', 404);
    }

}