<?php

namespace Krzysztofzylka\MicroFramework\Api;

use Krzysztofzylka\MicroFramework\ControllerApi;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Client;

/**
 * Secure
 * @package Api
 */
class Secure
{

    use Log;

    /**
     * Controller
     * @var ControllerApi
     */
    public ControllerApi $controller;

    /**
     * Content body is json
     * @return void
     */
    public function contentIsJson(): void
    {
        json_decode($this->controller->getBodyContent());

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->controller->response->error(
                'Bad request',
                400,
                'Body is not json'
            );
        }
    }

    /**
     * Validate content body (json) and response 400
     * @param array $keyList
     * @return void
     */
    public function bodyValidation(array $keyList): void
    {
        $contentBody = json_decode($this->controller->getBodyContent(), true);
        $contentBodyKeys = array_keys($contentBody);

        foreach ($keyList as $key) {
            if (!in_array($key, $contentBodyKeys)) {
                $this->controller->response->error(
                    'Invalid input data',
                    400,
                    'Require ' . $key
                );
            }
        }
    }

    /**
     * Allow from ip address
     * @param string|array $ips
     * @return void
     */
    public function allowIp(string|array $ips): void
    {
        if (is_string($ips)) {
            $ips = [$ips];
        }

        if (!in_array(Client::getIP(), $ips)) {
            $this->log('Access failed', 'WARNING', ['ip' => Client::getIP()]);

            $this->controller->response->error(
                'Not authorized',
                401,
                'IP address is incorrect'
            );
        }
    }

}