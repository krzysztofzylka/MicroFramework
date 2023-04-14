<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Krzysztofzylka\MicroFramework\bin\Trait\Prints;

class Help
{

    use Prints;

    /**
     * help
     */
    public function __construct()
    {
        $this->print('Help');
        $this->print('init                                          - Initialize project');
        $this->print('rebuild                                          - Rebuild project');
        $this->print('update                                        - Update project');

        $this->print('database update                               - Update database');
        $this->print('database update_info                          - Update database - info');

        $this->print('user create <login> <password> <is admin>     - Create user');

        $this->print('cron scheduled                                - Generate tasks');
        $this->print('cron runTasks                                 - Run cron tasks');

        $this->print('debug                                         - Debug');
    }

}