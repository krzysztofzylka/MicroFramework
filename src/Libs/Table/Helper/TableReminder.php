<?php

namespace Krzysztofzylka\MicroFramework\Libs\Table\Helper;

use Krzysztofzylka\MicroFramework\Libs\Table\Table;

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
     * Generate data key
     * @param Table $tableInstance
     * @return string
     */
    protected static function generateKey(Table $tableInstance): string
    {
        return 'table_' . $tableInstance->getId();
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

}