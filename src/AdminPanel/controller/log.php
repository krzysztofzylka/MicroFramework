<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\controller;

use Krzysztofzylka\MicroFramework\Controller;

class log extends Controller
{

    public function index()
    {
        $logs = [];

        $logLimit = 200;


        foreach (array_reverse($this->loadModel('log')->getList()) as $log) {
            $logArray = $this->Log->fileRead($log['path']);

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

        $this->table->activeSearch = false;
        $this->table->activePagination = false;
        $this->table->results = $logs;
        $this->table->columns = [
            'log.logLineNumber' => [
                'title' => 'Lp.',
                'width' => 150
            ],
            'log.datetime' => [
                'title' => 'Date',
                'width' => 220
            ],
            'log.level' => [
                'title' => 'Level',
                'width' => 30
            ],
            'log.message' => [
                'title' => 'Message'
            ]
        ];
        $this->loadView(['table' => $this->table->render()]);
    }

}