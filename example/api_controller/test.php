<?php

namespace api_controller;

use Exception;
use Krzysztofzylka\MicroFramework\ControllerApi;

class test extends ControllerApi {

    public bool $auth = false;

    public function insert() {
        $this->allowRequestMethod('POST');
        $this->contentBodyIsJson();
        $this->contentBodyValidate(['name', 'value']);
        $content = json_decode($this->getBodyContent(), true);

        try {
            $this->loadModel('test')->insert(['name' => $content['name'], 'value' => $content['value']]);
            $this->responseJson(['status' => 'success']);
        } catch (Exception) {
            $this->responseError('Internal error');
        }
    }

}