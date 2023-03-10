<?php

namespace config;

class ConfigDefault extends \Krzysztofzylka\MicroFramework\ConfigDefault {

    public bool $debug = true;
    public bool $api = true;

    public bool $database = true;
    public string $databaseName = 'microframework';
    public string $databasePassword = 'user';
    public string $databaseUsername = 'user';
    public bool $authControl = true;

    public bool $authControlDefaultRequireAuth = false;

    public string $defaultPage = '/index/index';

    public string $pageUrl = 'http://micro.local/';

    public bool $adminPanel = true;

}