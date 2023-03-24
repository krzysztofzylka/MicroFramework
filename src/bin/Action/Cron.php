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
use krzysztofzylka\SimpleLibraries\Library\Strings;

class Cron {

    use Prints;

    use Log;

    private Console $console;

    private array $scheduled;

    private Table $cronScheduledInstance;

    /**
     * help
     */
    public function __construct(Console $console)
    {
        $this->console = $console;

        if (!$console->cronPath) {
            $this->dtprint('Cron is disabled');
        } elseif (!Kernel::getConfig()->database) {
            $this->dprint('Database is disabled');
        }

        $this->prepareSite();
        $this->cronScheduledInstance = new Table('cron_scheduled');

        switch ($console->arg[2] ?? false) {
            case 'scheduled':
                $this->tprint('Start cron scheduled');
                $this->tprint('Load scheduled');
                $this->scheduled = require($console->cronPath);

                $this->tprint('Search tasks to run');

                foreach ($this->scheduled as $schedule) {
                    $cronFactory = CronExpression::factory($schedule['time']);

                    if ($cronFactory->isDue()) {
                        $this->tprint('Add task');
                        $this->cronScheduledInstance->insert([
                            'time' => $schedule['time'],
                            'model' => $schedule['model'],
                            'method' => $schedule['method'],
                            'args' => json_encode($schedule['args'])
                        ]);
                    }
                }

                break;
            case 'runTasks':
                $this->tprint('Run cron tasks');
                $this->tprint('Get tasts from database');
                $scheduled = $this->cronScheduledInstance->findAll(null, null, 'id ASC');

                if (!$scheduled) {
                    $this->dtprint('Tasks not found');
                }

                $this->tprint('Tasks: ' . count($scheduled));

                foreach ($scheduled as $id => $schedule) {
                    $schedule = $schedule['cron_scheduled'];
                    $this->tprint('Run task ' . $id + 1);

                    if (empty($schedule['model']) || empty($schedule['method'])) {
                        $this->tprint('Fail, model or/and method is empty');
                        $this->cronScheduledInstance->delete($schedule['id']);

                        continue;
                    }

                    try {
                        $controller = new Controller();
                        $model = $controller->loadModel($schedule['model']);
                        call_user_func_array([$model, $schedule['method']], json_decode($schedule['args'], true));
                    } catch (Exception $exception) {
                        $this->tprint('Cron failed');
                        $this->log('Cron fail', 'ERR', ['exception' => $exception, 'schedule' => $schedule]);
                    }

                    $this->cronScheduledInstance->delete($schedule['id']);
                }
                break;
            default:
                $this->dprint('Action not found');

                break;
        }
    }

    private function prepareSite() {
        Account::$accountId = -1;
    }

}