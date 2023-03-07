<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Extension\Account\Enum\AuthControlAction;

class ConfigDefault
{

    /**
     * Debug
     * @var bool
     */
    public bool $debug = false;

    /**
     * Default page
     * @var string
     */
    public string $defaultPage = 'index/index';

    /**
     * Default controller method
     * @var string
     */
    public string $defaultMethod = 'index';

    /** API */

    /**
     * API active
     * @var bool
     */
    public bool $api = false;

    /**
     * Api URI e.g. http://url.site/api for api
     * @var ?string
     */
    public ?string $apiUri = 'api';

    /** Database */

    /**
     * Active database
     * @var bool
     */
    public bool $database = false;

    /**
     * Database host
     * @var string
     */
    public string $databaseHost = '127.0.0.1';

    /**
     * Database username
     * @var string
     */
    public string $databaseUsername = '';

    /**
     * Database password
     * @var string
     */
    public string $databasePassword = '';

    /**
     * Database name
     * @var string
     */
    public string $databaseName = '';

    /**
     * Page URL
     * @var string
     */
    public string $pageUrl = 'http://127.0.0.1/';

    /** Extension Authorize */

    /**
     * Auth control
     * @var bool
     */
    public bool $authControl = false;

    /**
     * Default require auth
     * @var bool
     */
    public bool $authControlDefaultRequireAuth = true;

    /**
     * Auth control action
     * @var AuthControlAction
     */
    public AuthControlAction $authControlAction = AuthControlAction::exception;

    /**
     * Auth control redirect url
     * @var string
     */
    public string $authControlRedirect = '';

    /** E-Mail */

    /**
     * Activate E-Mail
     * @var bool
     */
    public bool $email = false;

    /**
     * E-Mail host
     * @var string
     */
    public string $emailHost = '';

    /**
     * E-Mail SMTP auth
     * @var bool
     */
    public bool $emailSMTPAuth = true;

    /**
     * E-Mail username
     * @var string
     */
    public string $emailUsername = '';

    /**
     * E-Mail password
     * @var string
     */
    public string $emailPassword = '';

    /**
     * E-Mail active SMTP secure
     * @var bool
     */
    public bool $emailSMTPSecure = true;

    /**
     * E-Mail port
     * @var int
     */
    public int $emailPort = 465;

    /**
     * E-Mail from in format: ['address@email.com', 'Company name']
     * @var array
     */
    public array $emailFrom = [];

}