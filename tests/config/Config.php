<?php

namespace config;

class Config extends \Krzysztofzylka\MicroFramework\ConfigDefault {

    public bool $api = true;

    public bool $database = true;
    public string $databaseName = 'microframework';
    public string $databasePassword = 'password';
    public string $databaseUsername = 'user';

}