<?php

use Krzysztofzylka\MicroFramework\ConfigDefault;
use Krzysztofzylka\MicroFramework\Kernel;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{

    public function testKernelCreate() {
        Kernel::create(__DIR__, false);
        $this->assertEquals(__DIR__, Kernel::getProjectPath());
    }

    public function testKernelData() {
        Kernel::setConfig(new ConfigDefault());
        Kernel::create(__DIR__, false);
        $this->assertEquals(null, Kernel::getData());

        $_POST = ['asdas', 'sdgsad', '<script>alert(\'sdg\')</script>'];
        Kernel::setConfig(new ConfigDefault());
        Kernel::create(__DIR__, false);

        $this->assertEquals(Kernel::getData(), ['asdas', 'sdgsad', '<script>alert(\\\'sdg\\\')</script>']);
    }

    public function testKernelConfig() {
        Kernel::setConfig(new ConfigDefault());
        $this->assertEquals(Kernel::getConfig(), new ConfigDefault());
    }

}