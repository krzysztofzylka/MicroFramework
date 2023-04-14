<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Api\Authorization;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Request;
use krzysztofzylka\SimpleLibraries\Library\Response;
use krzysztofzylka\SimpleLibraries\Library\Strings;

/**
 * Api controller
 * @package Controller
 */
class ControllerApi extends Controller
{

    use Log;

    /**
     * Is API controller
     * @var bool
     */
    public bool $isApi = true;

    /**
     * Authorization
     * @var bool
     */
    public bool $auth = true;

    /**
     * Constructor
     * - Automatic api authorization
     */
    public function __construct()
    {
        if ($this->auth) {
            if (isset($_SERVER['PHP_AUTH_USER']) || isset($_SERVER['PHP_AUTH_PW'])) {
                $username = isset($_SERVER['PHP_AUTH_USER']) ? htmlspecialchars($_SERVER['PHP_AUTH_USER']) : false;
                $password = isset($_SERVER['PHP_AUTH_PW']) ? htmlspecialchars($_SERVER['PHP_AUTH_PW']) : false;
                $auth = false;

                if ($username && $password) {
                    $auth = (new Authorization())->basic($username, $password);
                }

                if (!$auth) {
                    $this->log('Authorization failed', 'WARNING', ['username' => $username]);
                    $this->responseError('Not authorized', 401, 'Basic auth fail');
                }
            } elseif (isset($_SERVER['HTTP_APIKEY'])) {
                $apikey = Strings::escape($_SERVER['HTTP_APIKEY']);

                if (empty($apikey) || strlen($apikey) < 10) {
                    $this->responseError('Not authorized', 401, 'ApiKey: ' . $apikey);
                }

                $account = (new Table('account'))->find(['api_key' => $apikey]);

                if ($account) {
                    $auth = (new Authorization())->apikey($apikey);

                    if (!$auth) {
                        $this->log('Authorization failed', 'WARNING', ['apikey' => $apikey]);
                        $this->responseError('Not authorized', 401, 'Apikey auth fail');
                    }
                } else {
                    $this->log('Authorization failed', 'WARNING', ['apikey' => $apikey]);
                    $this->responseError('Not authorized', 401, 'Apikey auth fail');
                }
            } else {
                $this->log('Authorization failed', 'WARNING', ['Failed authorization type']);
                $this->responseError('Not authorized', 401, 'Failed authorization type');
            }
        }
    }

    /**
     * Response JSON error
     * @param string $message
     * @param int $code
     * @param ?string $detail
     * @return never
     */
    public function responseError(string $message, int $code = 500, ?string $detail = null): never
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

        $response = new Response();
        $response->json($data);
    }

    /**
     * Response JSON
     * @param array $data
     * @return never
     */
    public function responseJson(array $data): never
    {
        $response = new Response();
        $response->json($data);
    }

    /**
     * Check request method and response 400
     * @param string|array $method
     * @return void
     */
    public function allowRequestMethod(string|array $method): void
    {
        $method = is_string($method) ? [$method] : $method;

        foreach ($method as $key => $methodValue) {
            $method[$key] = strtolower($methodValue);
        }

        if (!in_array(strtolower($this->getRequestMethod()), $method)) {
            $this->responseError(
                'Invalid method',
                400,
                'Accepted method: ' . strtoupper(implode(',', $method))
            );
        }
    }

    /**
     * Get request method
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Content body is json and response 400
     * @return void
     */
    public function contentBodyIsJson(): void
    {
        json_decode($this->getBodyContent());

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->responseError(
                'Bad request',
                400,
                'Body is not json'
            );
        }
    }

    /**
     * Get body content
     * @return false|string
     */
    public function getBodyContent(): false|string
    {
        return Request::getInputContents();
    }

    /**
     * Validate content body (json) and response 400
     * @param array $keyList
     * @return void
     */
    public function contentBodyValidate(array $keyList): void
    {
        $contentBody = json_decode($this->getBodyContent(), true);
        $contentBodyKeys = array_keys($contentBody);

        foreach ($keyList as $key) {
            if (!in_array($key, $contentBodyKeys)) {
                $this->responseError(
                    'Invalid input data',
                    400,
                    'Require ' . $key
                );
            }
        }
    }

}