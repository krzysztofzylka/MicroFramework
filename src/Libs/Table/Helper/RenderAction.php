<?php

namespace Krzysztofzylka\MicroFramework\Libs\Table\Helper;

use Krzysztofzylka\MicroFramework\Libs\Table\Table;
use Krzysztofzylka\MicroFramework\View;

class RenderAction
{

    /**
     * Generate action
     * @param Table $tableInstance
     * @param string $action
     * @param array $params
     * @return string
     */
    public static function generate(Table $tableInstance, string $action, array $params = []): string
    {
        $data = [
            'layout' => 'table',
            'here' => View::$GLOBAL_VARIABLES['here'],
            'id' => $tableInstance->getId(),
            'action' => $action,
            'params' => $params

        ];

        return json_encode($data);
    }

}