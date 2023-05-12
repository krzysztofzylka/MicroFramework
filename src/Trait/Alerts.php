<?php

namespace Krzysztofzylka\MicroFramework\Trait;

use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use krzysztofzylka\SimpleLibraries\Library\Response;

/**
 * Alerts
 * @package Trait
 */
trait Alerts
{

    /**
     * Response alert
     * @param string $message
     * @param string $type OK, ERR, INFO, WARNING
     * @param string $title
     * @param array $params
     * @return never
     * @throws MicroFrameworkException
     */
    public function responseAlert(string $message, string $type = 'OK', string $title = '', array $params = []): never
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
                    'close' => $params['dialog']['close'] ?? false
                ],
                'layout' => 'toast',
                'pageReload' => $params['pageReload'] ?? false,
                'reloadForm' => $params['reloadForm'] ?? false,
                'redirect' => $params['redirect'] ?? false
            ]
        ]);
    }

}