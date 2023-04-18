<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Cron\CronExpression;
use Exception;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Trait\Log;

class Cron
{

    use Prints;

    use Log;

    private Console $console;

    /**
     * Cron
     */
    public function __construct(Console $console)
    {
        $this->console = $console;

        $cron = new \Krzysztofzylka\MicroFramework\Extension\Cron\Cron();

        if (!$cron->isActive()) {
            $this->dtprint('Cron is disabled');
        }

        switch ($console->arg[2] ?? false) {
            case 'scheduled':
                $this->tprint('Start cron scheduled');

                $count = $cron->runCronScheduled();

                $this->tprint('End cron scheduled, add ' . $count . ' tasks');
                break;
            case 'runTasks':
                $this->tprint('Start run tasks');

                $count = $cron->runTasks();

                $this->tprint('End run tasks, execute ' . $count . ' tasks');
                break;
            default:
                $this->dprint('Action not found');
                break;
        }
    }

}