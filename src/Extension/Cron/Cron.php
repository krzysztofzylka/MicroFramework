<?php

namespace Krzysztofzylka\MicroFramework\Extension\Cron;

use Cron\CronExpression;
use Exception;
use krzysztofzylka\DatabaseManager\Exception\DeleteException;
use krzysztofzylka\DatabaseManager\Exception\InsertException;
use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Trait\Log;

/**
 * Cron
 * @package Extension\Cron
 */
class Cron
{

    use Log;

    /**
     * Cron scheduled table instance
     * @var Table
     */
    private Table $cronScheduledInstance;

    /**
     * Cron scheduled list
     * @var array|mixed
     */
    private array $cronScheduled = [];

    /**
     * Cron file path
     * @var string
     */
    private string $cronFilePath;

    /**
     * Initialize
     * @return bool
     */
    public function __construct()
    {
        $this->cronFilePath = realpath(Kernel::getPath('config') . '/Cron.php');

        var_dump(Kernel::getPath('config'));

        if (!$this->isActive()) {
            return false;
        }

        $this->cronScheduled = include($this->cronFilePath);
        $this->cronScheduledInstance = new Table('cron_scheduled');

        return true;
    }

    /**
     * Get cron scheduled
     * @return array
     */
    public function getCronScheduled(): array
    {
        return $this->cronScheduled;
    }

    /**
     * Save scheduled task to database
     * @return int
     * @throws InsertException
     */
    public function runCronScheduled(): int
    {
        $count = 0;

        foreach ($this->getCronScheduled() as $schedule) {
            $cronFactory = CronExpression::factory($schedule['time']);

            if ($cronFactory->isDue()) {
                if (empty($schedule['model']) || empty($schedule['method'])) {
                    $this->log(
                        'Fail run cron',
                        'ERR',
                        ['schedule' => $schedule]
                    );

                    continue;
                }

                $this->cronScheduledInstance->insert([
                    'time' => $schedule['time'],
                    'model' => $schedule['model'],
                    'method' => $schedule['method'],
                    'args' => json_encode($schedule['args'])
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Cron is active
     * @return bool
     */
    public function isActive(): bool
    {
        if (!$this->cronFilePath) {
            return false;
        } elseif (!$_ENV['database_enabled']) {
            return false;
        }

        return true;
    }

    /**
     * Run tasks
     * @return int
     * @throws SelectException
     * @throws DeleteException
     */
    public function runTasks(): int
    {
        //todo cronlock for schedule task
        $count = 0;

        foreach ($this->getCronScheduledTasks() as $scheduledTask) {
            try {
                if (empty($scheduledTask['cron_scheduled']['model']) || empty($scheduledTask['cron_scheduled']['method'])) {
                    throw new Exception('Empty model or method');
                }

                $this->cleanData();
                $controller = new Controller();
                $model = $controller->loadModel($scheduledTask['cron_scheduled']['model']);
                call_user_func_array([$model, $scheduledTask['cron_scheduled']['method']], json_decode($scheduledTask['cron_scheduled']['args'], true));

                $this->deleteCronScheduledTask($scheduledTask['cron_scheduled']['id']);
                $count++;
            } catch (Exception $exception) {
                $this->log(
                    'Fail run cron task',
                    'ERR',
                    [
                        'scheduledTask' => $scheduledTask,
                        'exception' => $exception->getMessage()
                    ]
                );

                $this->deleteCronScheduledTask($scheduledTask['cron_scheduled']['id']);

                continue;
            }
        }

        return $count;
    }

    /**
     * Get cron scheduled tasks
     * @return array
     * @throws SelectException
     */
    public function getCronScheduledTasks(): array
    {
        return $this->cronScheduledInstance->findAll(null, null, 'id ASC');
    }

    /**
     * Delete cron scheduled task
     * @param int $id
     * @return bool
     * @throws DeleteException
     */
    public function deleteCronScheduledTask(int $id): bool
    {
        return $this->cronScheduledInstance->delete($id);
    }

    /**
     * Clean data
     * @return void
     */
    public function cleanData(): void
    {
        Account::$accountId = -1;
        Account::$account = null;
    }

}