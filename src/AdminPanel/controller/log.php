<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Table\Extra\Cell;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Debug;

class log extends Controller
{

    public function index()
    {
    }

    public function indexTable(): void
    {
        $this->layout = 'table';

        $logs = [];

        $logLimit = 500;

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

        $this->table->paginationLimit = 20;
        $this->table->pages = floor(count($logs) / $this->table->paginationLimit);
        $this->table->init();
        $this->table->activeSearch = false;
        $this->table->results = array_slice($logs, ($this->table->page - 1) * $this->table->paginationLimit, $this->table->paginationLimit);
        $this->table->columns = [
            'log.logLineNumber' => [
                'title' => __('micro-framework.admin_panel.log.lp'),
                'width' => 150,
                'value' => function (Cell $cell) {
                    return '<a class="ajaxlink" href="/admin_panel/log/detail/' . $cell->val . '">' . $cell->val . '</a>';
                }
            ],
            'log.datetime' => [
                'title' => __('micro-framework.admin_panel.log.date'),
                'width' => 220
            ],
            'log.level' => [
                'title' => __('micro-framework.admin_panel.log.level'),
                'width' => 100
            ],
            'log.message' => [
                'title' => __('micro-framework.admin_panel.log.message'),
                'maxChar' => 100
            ]
        ];
    }

    public function detail(string $id)
    {
        $this->layout = 'dialogbox';
        $this->title = 'Log detail ' . $id;
        $this->dialogboxWidth = 1000;
        $file = explode('-', $id)[0];
        $lineNumber = explode('-', $id)[1];
        $filePath = Kernel::getPath('logs') . '/' . $file . '.log.json';
        $result = null;
        $count = 0;

        foreach (file($filePath) as $line) {
            $count += 1;

            if ($count !== (int)$lineNumber) {
                continue;
            }

            $result = $line;

            break;
        }

        ob_start();
        Debug::print_r(array_merge(['logLineNumber' => $lineNumber, 'logFilePath' => realpath($filePath)], json_decode($result, true)));
        $detail = ob_get_clean();

        $this->loadView(['detail' => $detail]);
    }

}