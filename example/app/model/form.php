<?php

namespace app\model;

use Krzysztofzylka\MicroFramework\Exception\ValidationException;
use Krzysztofzylka\MicroFramework\Model;

class form extends Model
{

    public bool $useTable = false;

    public function loadValidation(): array
    {
        return [
            'validTest' => [
                'text' => [
                    'required',
                    'length' => ['min' => 6, 'max' => 48],
                    function ($content) : void {
                        if ($content === 'testing') {
                            throw new ValidationException('content is testing');
                        }
                    }
                ],
                'text2' => [
                    'required'
                ],
                'textarea' => [
                    'required',
                    'length' => ['min' => 10]
                ],
                'select' => [
                    'required'
                ],
                'date' => [
                    'isValidDate'
                ],
                'date2' => [
                    'isValidDate'
                ],
                'email' => [
                    'isEmail'
                ],
            ],
            'test' => [
                'input' => [
                    'required'
                ]
            ]
        ];
    }

}