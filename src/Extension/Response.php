<?php

namespace Krzysztofzylka\MicroFramework\Extension;

/**
 * Response extension
 */
class Response
{

    /**
     * Response JSON data
     * @param array $data
     * @param int|null $statusCode
     * @return never
     */
    public function json(array $data, ?int $statusCode = null): never
    {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!is_null($statusCode)) {
            http_response_code($statusCode);
        }

        die(json_encode($data));
    }

}