<?php

namespace config;

use Krzysztofzylka\MicroFramework\ConfigDefault;

class Config extends ConfigDefault {

    public bool $debug = true;
    public bool $api = true;

    public bool $database = true;
    public string $databaseName = 'microframework';
    public string $databasePassword = 'password';
    public string $databaseUsername = 'user';
    public bool $authControl = true;

    public bool $authControlDefaultRequireAuth = false;

}