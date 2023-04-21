<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use Krzysztofzylka\MicroFramework\bin\Action\Init;
use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Console\Generator\Table;

class Debug {

    public function __construct($console) {
        $data = [
            ['name' => 'argv', 'value' => json_encode($argv)],
            ['name' => 'disableDiePrint', 'value' => Console::$disableDiePrint ? 'TRUE' : 'FALSE'],
            ['name' => 'path', 'value' => $console->path],
            ['name' => 'resourcePath', 'value' => $console->resourcesPath],
            ['name' => 'vendorPath', 'value' => Init::getVendorPath($console->path)],
            ['name' => 'cronPath', 'value' => $console->cronPath],
            ['name' => 'config - database', 'value' => Kernel::getConfig()->database ? 'TRUE' : 'FALSE']
        ];

        $table = new Table();
        $table->addColumn('Name', 'name');
        $table->addColumn('Value', 'value');
        $table->setData($data);
        $table->render();
    }

}