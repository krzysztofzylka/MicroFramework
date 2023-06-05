<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Extension\Memcache\Memcache;

class setting extends Controller
{

    public function index(): void
    {
    }

    public function clearMemcache(): void
    {
        if (!Memcache::$active) {
            $this->response('memcached is not active.', 'ERR');
        }

        try {
            Memcache::$memcachedInstance->deleteMultiByKey('', Memcache::$memcachedInstance->getAllKeys());
        } catch (\Exception $exception) {
            $this->response(json_encode($exception->getMessage()));
        }

        $this->response('Success clear cache.');
    }

}