<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Api\Authorization;
use Krzysztofzylka\MicroFramework\Api\Response;
use Krzysztofzylka\MicroFramework\Api\Secure;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Request;
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
     * Response
     * @var Response
     */
    public Response $response;

    /**
     * Secure
     * @var Secure
     */
    public Secure $secure;

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
                    $this->response->error('Not authorized', 401, 'Basic auth fail');
                }
            } elseif (isset($_SERVER['HTTP_APIKEY'])) {
                $apikey = Strings::escape($_SERVER['HTTP_APIKEY']);

                if (empty($apikey) || strlen($apikey) < 10) {
                    $this->response->error('Not authorized', 401, 'ApiKey: ' . $apikey);
                }

                $account = (new Table('account'))->find(['api_key' => $apikey]);

                if ($account) {
                    $auth = (new Authorization())->apikey($apikey);

                    if (!$auth) {
                        $this->log('Authorization failed', 'WARNING', ['apikey' => $apikey]);
                        $this->response->error('Not authorized', 401, 'Apikey auth fail');
                    }
                } else {
                    $this->log('Authorization failed', 'WARNING', ['apikey' => $apikey]);
                    $this->response->error('Not authorized', 401, 'Apikey auth fail');
                }
            } else {
                $this->log('Authorization failed', 'WARNING', ['Failed authorization type']);
                $this->response->error('Not authorized', 401, 'Failed authorization type');
            }
        }
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
            $this->response->error(
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
     * Get body content
     * @return false|string
     */
    public function getBodyContent(): false|string
    {
        return Request::getInputContents();
    }

}