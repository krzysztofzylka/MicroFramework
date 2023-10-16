<?php

namespace api\controller;

use Exception;
use Krzysztofzylka\MicroFramework\Api\Enum\ContentType;
use Krzysztofzylka\MicroFramework\ControllerApi;

class test extends ControllerApi {

    public bool $auth = false;

    public function insert(): void
    {
        $this->secure->allowRequestMethod('POST');
        $this->secure->contentIsJson();
        $this->secure->bodyValidation(['name', 'value']);
        $content = $this->getBodyContent(ContentType::Json);

        try {
            $this->loadModel('test')->insert(['name' => $content['name'], 'value' => $content['value']]);
            $this->response->json(['status' => 'success']);
        } catch (Exception) {
            $this->response->error('Internal error');
        }
    }

}