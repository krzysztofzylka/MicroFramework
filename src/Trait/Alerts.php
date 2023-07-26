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
     * Alerts and ajax simple function
     * @throws MicroFrameworkException
     */
    public function response(
        string $message,
        string $type = 'OK',
        bool $reloadPage = false,
        bool $closeDialog = false
    ): never
    {
        $this->responseAlert($message, $type, '', [
            'pageReload' => $reloadPage,
            'dialog' => [
                'close' => $closeDialog
            ]
        ]);
    }

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

        if (!empty($message) && isset($params['redirect']) && $params['redirect']) {
            $_SESSION['redirectAlert'] = json_encode(['message' => $message, 'type' => $type, 'title' => $title]);
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