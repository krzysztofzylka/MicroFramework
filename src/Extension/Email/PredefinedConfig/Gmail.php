<?php

namespace Krzysztofzylka\MicroFramework\Extension\Email\PredefinedConfig;

class Gmail
{

    public bool $emailSMTPAuth = true;
    public string $emailSMTPSecure = 'tls';
    public int $emailPort = 587;
    public string $emailHost = 'smtp.gmail.com';

}