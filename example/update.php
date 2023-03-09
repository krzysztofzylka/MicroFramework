<?php

use config\Config;
use Krzysztofzylka\MicroFramework\Autoload;
use Krzysztofzylka\MicroFramework\Executable\DatabaseUpdater;
use Krzysztofzylka\MicroFramework\Kernel;

require('../vendor/autoload.php');

$kernel = new Kernel();
Kernel::create(__DIR__);
new Autoload(Kernel::getProjectPath());
Kernel::setConfig(new Config());
Kernel::configDatabaseConnect();

$databaseUpdater = new DatabaseUpdater();
$databaseUpdater->run();