<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use krzysztofzylka\SimpleLibraries\Library\Response;

trait ApiController {

    public bool $isApi = true;

    /**
     * Response json
     * @param array $data
     * @return never
     */
    public function responseJson(array $data) : never {
        $response = new Response();
        $response->json($data);
    }

}