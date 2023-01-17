<?php

namespace Krzysztofzylka\MicroFramework;

class ConfigDefault {

    /**
     * Default page
     * @var string
     */
    public $defaultPage = 'index/index';

    /**
     * Default controller method
     * @var string
     */
    public $defaultMethod = 'index';

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

}