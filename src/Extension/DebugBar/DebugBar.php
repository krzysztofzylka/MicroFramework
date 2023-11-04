<?php

namespace Krzysztofzylka\MicroFramework\Extension\DebugBar;

use DebugBar\DataCollector\AggregatedCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\LocalizationCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\JavascriptRenderer;
use DebugBar\StandardDebugBar;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DebugBar
{

    /**
     * Is init
     * @var bool
     */
    private static bool $init = false;

    /**
     * StandardDebugBar instance
     * @var StandardDebugBar
     */
    public static StandardDebugBar $standardDebugBar;

    /**
     * StandardDebugBarRenderer instance
     * @var JavascriptRenderer
     */
    private static JavascriptRenderer $standardDebugBarRenderer;

    /**
     * Add message
     * @param mixed $message
     * @param mixed $label
     * @return void
     */
    public static function addMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['messages']->addMessage($message, $label);
    }

    /**
     * Time start
     * @param string $name
     * @param string $label
     * @return void
     */
    public static function timeStart(string $name = 'time', string $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['time']->startMeasure($name, $label);
    }

    /**
     * Time stop
     * @param string $name
     * @return void
     */
    public static function timeStop(string $name = 'time'): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['time']->stopMeasure($name);
    }

    /**
     * Add throwable
     * @param $exception
     * @return void
     */
    public static function addThrowable($exception): void
    {
        self::$standardDebugBar['exceptions']->addThrowable($exception);
    }

    /**
     * Render header
     * @return string
     */
    public static function renderHeader(): string
    {
        if (!self::$init) {
            return '';
        }

        return self::$standardDebugBarRenderer->renderHead();
    }

    /**
     * Render
     * @return string
     */
    public static function render(): string
    {
        if (!self::$init) {
            return '';
        }

        return self::$standardDebugBarRenderer->render();
    }

    /**
     * Init
     * @return void
     * @throws SimpleLibraryException
     */
    public function init(): void
    {
        self::$init = true;
        $this->copyAssets();
        self::$standardDebugBar = new StandardDebugBar();
        self::$standardDebugBarRenderer = self::$standardDebugBar->getJavascriptRenderer();
        self::$standardDebugBar->addCollector(new ConfigCollector($_ENV));
    }

    /**
     * Copy assets
     * @return void
     * @throws SimpleLibraryException
     */
    private function copyAssets(): void
    {
        $from = __DIR__ . '/../../../vendor/maximebf/debugbar/src/DebugBar/Resources';
        $to = Kernel::getPath('assets') . '/../vendor/maximebf/debugbar/src/DebugBar/Resources';

        if (file_exists($to)) {
            return;
        }

        File::mkdir($to);
        $this->copyDirectory($from, $to);
    }

    /**
     * Copy directory
     * @param $source
     * @param $destination
     * @return void
     */
    private function copyDirectory($source, $destination): void
    {
        if (is_dir($source) && is_dir($destination)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $src = $item->getPathname();
                $dst = $destination . '/' . $iterator->getSubPathName();
                if ($item->isDir()) {
                    mkdir($dst);
                } else {
                    copy($src, $dst);
                }
            }
        }
    }

}