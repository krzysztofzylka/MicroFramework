<?php

namespace Krzysztofzylka\MicroFramework\Extension\DebugBar;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\LocalizationCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DebugBarException;
use DebugBar\JavascriptRenderer;
use DebugBar\StandardDebugBar;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Debug bar
 */
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
     * Add message
     * @param mixed $message
     * @param mixed $label
     * @return void
     */
    public static function addFrameworkMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['framework']->addMessage($message, $label);
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
     * Add model message
     * @param $model
     * @return void
     */
    public static function addModelMessage($model): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['models']->addMessage($model);
    }

    /**
     * Add log message
     * @param mixed $message
     * @param string $label
     * @return void
     */
    public static function addLogMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['logs']->addMessage($message, $label);
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
     * @throws DebugBarException
     */
    public function init(): void
    {
        self::$init = true;
        $this->copyAssets();
        self::$standardDebugBar = new StandardDebugBar();
        self::$standardDebugBarRenderer = self::$standardDebugBar->getJavascriptRenderer();
        self::$standardDebugBar->addCollector(new MessagesCollector('framework'));
        self::$standardDebugBar->addCollector(new ConfigCollector($_ENV, 'ENV'));
        self::$standardDebugBar->addCollector(new MessagesCollector('models'));
        self::$standardDebugBar->addCollector(new MessagesCollector('logs'));
    }

    /**
     * Add PDO collection
     * @return void
     * @throws DebugBarException
     */
    public static function addPdoCollection(): void
    {
        if (!self::$init) {
            return;
        }

        if ($_ENV['DATABASE']) {
            $pdoCollector = new PDOCollector();
            $pdoCollector->addConnection(DatabaseManager::$connection->getConnection());

            self::$standardDebugBar->addCollector($pdoCollector);
        }
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
        File::copyDirectory($from, $to);
    }

}