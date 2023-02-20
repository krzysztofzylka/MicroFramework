<?php

namespace config;

use Krzysztofzylka\MicroFramework\ConfigDefault;

class Config extends ConfigDefault {

    public bool $debug = true;
    public bool $api = true;

    public bool $database = true;
    public string $databaseName = 'microframework';
    public string $databasePassword = 'user';
    public string $databaseUsername = 'user';
    public bool $authControl = false;

    public bool $authControlDefaultRequireAuth = false;

}