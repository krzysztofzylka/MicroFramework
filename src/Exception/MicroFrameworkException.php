<?php

namespace Krzysztofzylka\MicroFramework\Exception;

use Exception;

/**
 * Microframework exception (main)
 * @package Exception
 */
class MicroFrameworkException extends Exception
{

    /**
     * Hidden message
     * @var mixed
     */
    private mixed $hiddenMessage;

    public function __construct(string $message = 'Server error', ?int $code = 500)
    {
        parent::__construct($message, $code);
    }

    /**
     * Get hidden message
     * @return string
     */
    public function getHiddenMessage(): string
    {
        return $this->hiddenMessage ?? '';
    }

    /**
     * Set hidden message
     * @param mixed $message
     * @return void
     */
    public function setHiddenMessage(mixed $message): void
    {
        $this->hiddenMessage = $message;
    }

}