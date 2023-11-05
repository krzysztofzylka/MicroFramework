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
        $response = new \krzysztofzylka\SimpleLibraries\Library\Response();
        $response->json($data, $statusCode);
    }

}