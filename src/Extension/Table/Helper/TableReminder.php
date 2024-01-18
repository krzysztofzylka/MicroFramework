<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Helper;

use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;

class TableReminder
{

    /**
     * Save data
     * @param Table $tableInstance
     * @param array $newData
     * @return void
     */
    public static function saveData(Table $tableInstance, array $newData): void
    {
        $data = array_merge(self::getData($tableInstance), $newData);

        $_SESSION[self::generateKey($tableInstance)] = json_encode($data);
    }

    /**
     * Get saved data
     * @param Table $tableInstance
     * @return array
     */
    public static function getData(Table $tableInstance): array
    {
        if (!isset($_SESSION[self::generateKey($tableInstance)])) {
            return [];
        }

        return json_decode($_SESSION[self::generateKey($tableInstance)], true);
    }

    /**
     * Generate save time
     * @param int $day
     * @return int
     */
    protected static function getTime(int $day = 365): int
    {
        return 86400 * $day;
    }

    /**
     * Generate data key
     * @param Table $tableInstance
     * @return string
     */
    protected static function generateKey(Table $tableInstance): string
    {
        return 'table_' . $tableInstance->getId();
    }

}