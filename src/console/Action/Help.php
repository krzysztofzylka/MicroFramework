<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use krzysztofzylka\SimpleLibraries\Library\Console\Generator\Help as HelpGenerator;

class Help
{

    public function __construct()
    {
        $help = new HelpGenerator();
        $help->addHeader('Help');
        $help->addHelp('init', 'Initialize project');
        $help->addHelp('rebuild', 'Rebuild project');
        $help->addHelp('database update', 'Update database');
        $help->addHelp('database update_info', 'Update database info');
        $help->addHelp('user create <login> <password> <is admin>', 'Create user');
        $help->addHelp('cron scheduled', 'Generate tasks');
        $help->addHelp('cron runTasks', 'Run cron tasks');
        $help->addHelp('debug', 'Debug');
        $help->addHeader('Params');
        $help->addHelp('-projectPath <projectPath>', 'Define project path');
        $help->render();
    }

}