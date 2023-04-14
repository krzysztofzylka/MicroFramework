<?php

namespace app\model;

use Krzysztofzylka\MicroFramework\Model;

class example extends Model {

    public function loadValidation()
    {
        return [
            'example' => [
                'name' => [
                    'required',
                    'length' => ['min' => 4]
                ],
                'status' => [
                    'required'
                ]
            ]
        ];
    }

}