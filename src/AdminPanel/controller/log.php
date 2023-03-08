<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\controller;

use Krzysztofzylka\MicroFramework\Controller;

class log extends Controller
{

    public function index()
    {
        $logs = [];

        $logLimit = 1000;


        foreach (array_reverse($this->loadModel('paLog')->getList()) as $log) {
            $logArray = $this->PaLog->fileRead($log['path']);

            if ($logLimit > 0) {
                if (count($logArray) > $logLimit) {
                    $logArray = array_slice($logArray, count($logArray) - $logLimit);
                    $logs = array_merge($logs, $logArray);

                    break;
                }
            }

            $logLimit -= count($logArray);
            $logs = array_merge($logs, $logArray);
        }

        usort($logs, function ($a, $b) {
            return strtotime($b['log']['datetime']) - strtotime($a['log']['datetime']);
        });

        $this->loadView([
            'logs' => $logs
        ]);
    }

}