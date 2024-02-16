<?php

namespace Krzysztofzylka\MicroFramework\Console\Traits;

use Cron\CronExpression;
use Exception;
use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Kernel;

trait InternalConsoleCron
{

    /**
     * Cron runner
     * @return void
     * @throws Exception
     */
    private function cronRun(): void
    {
        $this->print('Cron start');
        $cronFile = File::repairPath($this->path . '/cron.json');

        if (!file_exists($cronFile)) {
            $this->print('Not found cron.json file in ' . $this->path, 'red', true);
        }

        try {
            new Kernel($this->path);

            $cron = json_decode(file_get_contents($cronFile), true);

            foreach ($cron as $key => $schedule) {
                $this->print('Execute schedule ' . $key);
                $cronExpression = new CronExpression($schedule['time']);

                if ($cronExpression->isDue()) {
                    if (!isset($schedule['model']) || !isset($schedule['method'])) {
                        $this->print('Shedule not found model and method params', 'yellow');

                        continue;
                    }

                    try {
                        $controller = new Controller();
                        $model = $controller->loadModel($schedule['model']);
                        call_user_func_array([$model, $schedule['method']], $schedule['args'] ?? []);
                        $this->print('Success', 'green');
                    } catch (\Throwable $throwable) {
                        Log::log('Failed execute schedule', 'ERROR', ['message' => $throwable->getMessage()]);
                        $this->print('Failed execute schedule', 'yellow');

                        continue;
                    }
                }
            }
        } catch (\Throwable $throwable) {
            Log::log('Cron error', 'ERROR', ['message' => $throwable->getMessage()]);
            $this->print('Failed read cron.json file', 'red', true);
        }
    }

}