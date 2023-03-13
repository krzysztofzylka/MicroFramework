<?php

namespace Krzysztofzylka\MicroFramework\Exception;

/**
 * Database exception
 * @package Exception
 */
class DatabaseException extends MicroFrameworkException
{

    public function __construct(string $message = 'Database error.')
    {
        $this->setHiddenMessage($message);

        parent::__construct('Database error.', 404);
    }

}