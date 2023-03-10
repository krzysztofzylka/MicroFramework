<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use krzysztofzylka\SimpleLibraries\Library\Response;

trait Alerts {

    /**
     * Response alert
     * @param string $message
     * @param string $type
     * @param string $title
     * @return never
     * @throws MicroFrameworkException
     */
    public function responseAlert(string $message, string $type = 'OK', string $title = ''): never
    {
        $type = strtoupper($type);

        if (!in_array($type, ['OK', 'ERR', 'INFO', 'WARNING'])) {
            throw new MicroFrameworkException('Type error');
        }

        (new Response())->json([
            'message' => $message,
            'title' => $title,
            'type' => $type,
            'ajaxLoaderConfig' => [
                'dialog' => [
                    'close' => false
                ],
                'layout' => 'toast',
                'pageReload' => false,
                'reloadForm' => false,
                'redirect' => false
            ]
        ]);
    }

}