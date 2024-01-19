<?php

namespace Krzysztofzylka\MicroFramework\Exception;

use Exception;
use Throwable;

/**
 * Framework exception
 */
class HiddenException extends Exception
{

    /**
     * Hidden message
     * @var string
     */
    protected string $hiddenMessage = '';

    /**
     * Get hidden message
     * @return string
     */
    public function getHiddenMessage(): string
    {
        return $this->hiddenMessage;
    }

    /**
     * Constructor
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->hiddenMessage = $message;

        parent::__construct('An error occurred', $code, $previous);
    }

}