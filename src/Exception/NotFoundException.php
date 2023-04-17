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

        parent::__construct(__('micro-framework.exceptions.not_found.object_not_found'), 404);
    }

}