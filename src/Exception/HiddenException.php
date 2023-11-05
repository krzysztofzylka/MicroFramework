<?php

namespace Krzysztofzylka\MicroFramework\Exception;

use Exception;

/**
 * Framework exception
 */
class HiddenException extends Exception
{

    public string $hiddenMessage = '';

    /**
     * Get hidden message
     * @return string
     */
    public function getHiddenMessage(): string
    {
        return $this->hiddenMessage;
    }

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->hiddenMessage = $message;

        parent::__construct('An error occured', $code, $previous);
    }

}