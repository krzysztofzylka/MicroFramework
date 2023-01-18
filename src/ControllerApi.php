<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Api\Authorization;
use Krzysztofzylka\MicroFramework\Api\Enum\AuthorizationType;
use krzysztofzylka\SimpleLibraries\Library\Response;

class ControllerApi extends Controller {

    /**
     * Is API controller
     * @var bool
     */
    public bool $isApi = true;

    /**
     * Authorization
     * @var bool
     */
    public bool $auth = false;

    /**
     * Authorization type
     * @var AuthorizationType
     */
    public AuthorizationType $authorizationType = AuthorizationType::basic;

    /**
     * Constructor
     * - Automatic authorization
     */
    public function __construct() {
        if ($this->auth) {
            if ($this->authorizationType === AuthorizationType::basic) {
                $auth = false;
                $username = isset($_SERVER['PHP_AUTH_USER']) ? htmlspecialchars($_SERVER['PHP_AUTH_USER']) : false;
                $password = isset($_SERVER['PHP_AUTH_PW']) ? htmlspecialchars($_SERVER['PHP_AUTH_PW']) : false;

                if ($username && $password) {
                    $authorization = new Authorization();

                    $auth = $authorization->basic($username, $password);
                }

                if (!$auth) {
                    $this->responseError('Not authorized', 401);
                }
            }
        }
    }

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

    /**
     * Get request method
     * @return string
     */
    public function getRequestMethod() : string {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get body content
     * @return false|string
     */
    public function getBodyContent() : false|string {
        return file_get_contents('php://input');
    }

}