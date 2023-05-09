<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;

class Cron
{

    use Log;

    private $console;

    /**
     * Cron
     */
    public function __construct($console)
    {
        $this->console = $console;
        $this->console->initKernel();

        var_dump($_ENV);

        $cron = new \Krzysztofzylka\MicroFramework\Extension\Cron\Cron();

        if (!$cron->isActive()) {
            Prints::print('Cron is disabled', false, true);
        }

        switch ($console->arg[2] ?? false) {
            case 'scheduled':
                Prints::print('Start cron scheduled', true);

                $count = $cron->runCronScheduled();

                Prints::print('End cron scheduled, add ' . $count . ' tasks', true, true);
                break;
            case 'runTasks':
                Prints::print('Start run tasks', true);

                $count = $cron->runTasks();

                Prints::print('End run tasks, execute ' . $count . ' tasks', true, true);
                break;
            default:
                Prints::print('Action not found', false, true);
                break;
        }
    }

}