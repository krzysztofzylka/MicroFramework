<?php

namespace app\controller;

use Exception;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Email\Extra\SendEmail;

class email extends Controller
{

    public function index()
    {
        try {
            $email = new \Krzysztofzylka\MicroFramework\Extension\Email\Email();

            /** @var SendEmail $newEmail */
            $newEmail = $email->newEmail();

            if (!$newEmail) {
                throw new Exception('EMail extension is disabled');
            }

            $newEmail->addAddress('address@email', 'email');

            if ($newEmail->send('test', 'testmail')) {
                echo 'ok';
            } else {
                echo 'fail';
            }
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

}