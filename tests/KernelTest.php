<?php

use PHPUnit\Framework\TestCase;
use Krzysztofzylka\MicroFramework\Kernel;

class KernelTest extends TestCase {

    public function testInitPaths() {
        Kernel::initPaths('', false);
        $this->assertEquals('', Kernel::getProjectPath());
    }

    public function testPaths() {
        Kernel::initPaths('', false);

        $paths = [
            'public' => '/public',
            'controller' => '/app/controller',
            'model' => '/app/model',
            'view' => '/app/view',
            'api_controller' => '/api/controller',
            'pa_view' => '/admin_panel/view',
            'pa_controller' => '/admin_panel/controller',
            'pa_model' => '/admin_panel/model',
            'storage' => '/storage',
            'logs' => '/storage/logs',
            'database_updater' => '/database_updater',
            'assets' => '/public/assets',
            'config' => '/config',
            'env' => '/env',
            'service' => '/service'
        ];

        foreach ($paths as $key => $value) {
            $this->assertEquals($value, Kernel::getPath($key));
        }
    }

    public function testLoadEnv() {
        Kernel::initPaths('', false);
        Kernel::loadEnv();
        $this->assertIsArray($_ENV);
        $this->assertNotEmpty($_ENV);
    }

    public function testGetProjectPath() {
        Kernel::initPaths('', false);
        $this->assertEquals('', Kernel::getProjectPath());
    }

    public function testGetDataPath() {
        Kernel::initPaths('', false);
        $this->assertNull(Kernel::getData());
        $_POST['test'] = 'test_value';
        $this->assertIsArray(Kernel::getData());
        $this->assertEquals(Kernel::getData(), ['test' => 'test_value']);
    }

}
