<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use krzysztofzylka\SimpleLibraries\Library\Console\Generator\Help as HelpGenerator;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;

class Help
{

    public function __construct()
    {
        Prints::print('Help');
        $help = new HelpGenerator();
        $help->addHelp('init', 'Initialize project');
        $help->addHelp('rebuild', 'Rebuild project');
        $help->addHelp('database update', 'Update database');
        $help->addHelp('database update_info', 'Update database info');
        $help->addHelp('user create <login> <password> <is admin>', 'Create user');
        $help->addHelp('cron scheduled', 'Generate tasks');
        $help->addHelp('cron runTasks', 'Run cron tasks');
        $help->addHelp('debug', 'Debug');
        $help->render();

        Prints::print('');
        Prints::print('Params');
        $help = new HelpGenerator();
        $help->addHelp('-projectPath <projectPath>', 'Define project path');
        $help->render();
    }

}