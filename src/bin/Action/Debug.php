<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Console\Generator\Table;

class Debug
{

    use Prints;

    /**
     * help
     */
    public function __construct(Console $console)
    {
        $this->print('Debug');

        $data = [
            ['name' => 'action', 'value' => $console->action],
            ['name' => 'arg', 'value' => json_encode($console->arg)],
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