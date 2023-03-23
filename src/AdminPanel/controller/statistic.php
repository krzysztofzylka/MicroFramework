<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\controller;

use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Controller;

class statistic extends Controller
{

    public function index()
    {
        $labels = [];
        $unique = [];
        $visits = [];

        $statistics = (new Table('statistic'))->findAll([
            new Condition('statistic.date', '>=', date('Y-m-d', strtotime('-1 month')))
        ], 'statistic.date ASC');

        foreach ($statistics as $statistic) {
            $labels[] = $statistic['statistic']['date'];
            $unique[] = $statistic['statistic']['unique'];
            $visits[] = $statistic['statistic']['visits'];
        }

        $this->loadView([
            'labels' => '["' . implode('", "', $labels) . '"]',
            'unique' => '[' . implode(', ', $unique) . ']',
            'visits' => '[' . implode(', ', $visits) . ']',
        ]);
    }

}