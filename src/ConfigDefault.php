<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Extension\Account\Enum\AuthControlAction;
use Krzysztofzylka\MicroFramework\Extension\Email\Enum\PredefinedConfig;

/**
 * Default config
 * @package Config
 */
class ConfigDefault
{

    /**
     * Debug
     * @var bool
     */
    public bool $debug = false;

    /**
     * Show all errors
     * @var bool
     */
    public bool $showAllErrors = true;

    /**
     * Default page
     * @var string
     */
    public string $defaultPage = '/index/index';

    /**
     * Default controller
     * @var string
     */
    public string $defaultController = 'index';

    /**
     * Default controller method
     * @var string
     */
    public string $defaultMethod = 'index';

    /**
     * Admin panel
     * @var bool
     */
    public bool $adminPanel = false;

    /**
     * Admin panel URI e.g. http://url.site/admin_panel
     * @var ?string
     */
    public ?string $adminPanelUri = 'admin_panel';

    /**
     * Statistics
     * @var bool
     */
    public bool $statistics = false;

    /**
     * Translaction
     * @var string
     */
    public string $translation = 'english';

    /** API */

    /**
     * API active
     * @var bool
     */
    public bool $api = false;

    /**
     * Api URI e.g. http://url.site/api
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

    /** Extension E-Mail */

    /**
     * Email predefined config
     * @var ?PredefinedConfig
     */
    public ?PredefinedConfig $emailPredefinedConfig = null;

    /**
     * Enable E-Mail
     * @var bool
     */
    public bool $email = false;

    /**
     * Enable E-Mail debug
     * @var bool
     */
    public bool $emailDebug = false;

    /**
     * Email is SMTP
     * @var bool
     */
    public bool $emailIsSMTP = true;

    /**
     * Email host
     * @var string
     */
    public string $emailHost = '';

    /**
     * Email SMTP auth
     * @var bool
     */
    public bool $emailSMTPAuth = true;

    /**
     * Email username
     * @var string
     */
    public string $emailUsername = '';

    /**
     * Email password
     * @var string
     */
    public string $emailPassword = '';

    /**
     * Email charset
     * @var string
     */
    public string $emailCharset = 'UTF-8';

    /**
     * Email SMTP secure
     * @var string
     */
    public string $emailSMTPSecure = 'ssl';

    /**
     * Email port
     * @var int
     */
    public int $emailPort = 25;

    /**
     * Email from, default $emailUsername
     * @var ?string
     */
    public ?string $emailFrom = null;

    /**
     * Email from name
     * @var string
     */
    public string $emailFromName = '';

    /** View */

    /**
     * Disable view cache
     * @var bool
     */
    public bool $viewDisableCache = true;

    /** Logger */

    /**
     * Active logger
     * @var bool
     */
    public bool $logger = false;

    /**
     * Logger url
     * @var string
     */
    public string $loggerUrl = '';

    /**
     * Logger ApiKey
     * @var string
     */
    public string $loggerApiKey = '';

    /**
     * Logger SiteKey
     * @var string
     */
    public string $loggerSiteKey = '';

    /**
     * Logger username (for basic auth)
     * @var string
     */
    public string $loggerUsername = '';

    /**
     * Logger password  (for basic auth)
     * @var string
     */
    public string $loggerPassword = '';

}