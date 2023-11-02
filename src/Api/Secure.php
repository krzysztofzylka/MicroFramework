<?php

namespace Krzysztofzylka\MicroFramework\Api;

use Krzysztofzylka\MicroFramework\ControllerApi;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\Client;
use krzysztofzylka\SimpleLibraries\Library\Json;

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
     * @throws SimpleLibraryException
     */
    public function contentIsJson(): void
    {
        if (Json::isJson($this->controller->getBodyContent())) {
            return;
        }

        $this->controller->response->error(
            'Bad request',
            400,
            'Body is not json'
        );
    }

    /**
     * Validate content body (json) and response 400
     * @param array $keyList
     * @return void
     * @throws SimpleLibraryException
     */
    public function bodyValidation(array $keyList): void
    {
        $contentBody = json_decode($this->controller->getBodyContent(), true);
        $contentBodyKeys = array_keys($contentBody);

        foreach ($keyList as $key) {
            if (in_array($key, $contentBodyKeys)) {
                continue;
            }

            $this->controller->response->error(
                'Invalid input data',
                400,
                'Require ' . $key
            );
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

        if (in_array(Client::getIP(), $ips)) {
            return;
        }

        $this->log('Access failed', 'WARNING', ['ip' => Client::getIP()]);

        $this->controller->response->error(
            'Not authorized',
            401,
            'IP address is incorrect'
        );
    }

    /**
     * Check request method and response 400
     * @param string|array $method
     * @return void
     */
    public function allowRequestMethod(string|array $method): void
    {
        $method = !is_array($method) ? [$method] : $method;

        foreach ($method as $key => $methodValue) {
            $method[$key] = strtolower($methodValue);
        }

        if (in_array(strtolower($this->controller->getRequestMethod()), $method)) {
            return;
        }

        $this->controller->response->error(
            'Invalid method',
            400,
            'Accepted method: ' . strtoupper(implode(',', $method))
        );
    }

}