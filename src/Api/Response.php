<?php

namespace Krzysztofzylka\MicroFramework\Api;

use Krzysztofzylka\MicroFramework\ControllerApi;

/**
 * Response
 * @package Api
 */
class Response
{

    /**
     * Controller
     * @var ControllerApi
     */
    public ControllerApi $controller;

    /**
     * Response JSON
     * @param array $data response data
     * @return never
     */
    public function json(array $data): never
    {
        $response = new \krzysztofzylka\SimpleLibraries\Library\Response();
        $response->json($data);
    }

    /**
     * Response JSON error
     * @param string $message message
     * @param int $code error code
     * @param ?string $detail error detail
     * @return never
     */
    public function error(string $message, int $code = 500, ?string $detail = null): never
    {
        $data = [
            'error' => [
                'message' => $message,
                'code' => $code
            ]
        ];

        if ($detail) {
            $data['error']['detail'] = $detail;
        }

        http_response_code($code);

        $this->json($data);
    }

}