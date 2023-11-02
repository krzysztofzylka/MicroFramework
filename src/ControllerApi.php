<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Exception\ConditionException;
use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Exception\TableException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Api\Authorization;
use Krzysztofzylka\MicroFramework\Api\Enum\ContentType;
use Krzysztofzylka\MicroFramework\Api\Response;
use Krzysztofzylka\MicroFramework\Api\Secure;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
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
     * Get request method
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get body content
     * @param ContentType $contentType Content type, default string
     * @return false|string
     * @throws SimpleLibraryException
     */
    public function getBodyContent(ContentType $contentType = ContentType::String): false|string
    {
        switch ($contentType) {
            case ContentType::String:
                return Request::getInputContents();
            case ContentType::Json:
                $this->secure->contentIsJson();

                return json_decode(Request::getInputContents(), true);
        }

        return false;
    }

    /**
     * Authorize API
     * @return void
     * @throws ConditionException
     * @throws SelectException
     * @throws TableException
     */
    public function _autoAuth(): void
    {
        if ($this->auth) {
            if (isset($_SERVER['PHP_AUTH_USER']) || isset($_SERVER['PHP_AUTH_PW'])) {
                if (!(new Authorization())->basic($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                    $this->log('Authorization failed', 'WARNING', ['username' => htmlspecialchars($_SERVER['PHP_AUTH_USER'])]);
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
                $this->log(
                    'Authorization failed',
                    'WARNING',
                    [
                        'message' => 'Failed authorization type',
                        'server' => $_SERVER
                    ]
                );

                $this->response->error('Not authorized', 401, 'Failed authorization type');
            }
        }
    }

}