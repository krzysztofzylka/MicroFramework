<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use krzysztofzylka\SimpleLibraries\Library\Console\Generator\Table;

class Debug
{

    public function __construct($console)
    {
        $data = [
            ['name' => 'path', 'value' => $console->path]
        ];

        $table = new Table();
        $table->addColumn('Name', 'name');
        $table->addColumn('Value', 'value');
        $table->setData($data);
        $table->render();
    }

}