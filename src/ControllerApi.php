<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\SimpleLibraries\Library\Response;

class ControllerApi extends Controller {

    /**
     * Is API controller
     * @var bool
     */
    public bool $isApi = true;

    /**
     * Response JSON
     * @param array $data
     * @return never
     */
    public function responseJson(array $data) : never {
        $response = new Response();
        $response->json($data);
    }

    /**
     * Response JSON error
     * @param string $message
     * @param int $code
     * @return never
     */
    public function responseError(string $message, int $code = 400) : never {
        http_response_code($code);

        $response = new Response();
        $response->json(
            [
                'error' => [
                    'message' => $message,
                    'code' => $code
                ]
            ]
        );
    }

}