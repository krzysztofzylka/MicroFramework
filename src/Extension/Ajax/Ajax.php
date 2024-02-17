<?php

namespace Krzysztofzylka\MicroFramework\Extension\Ajax;

use Exception;
use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;

class Ajax
{

    /**
     * Init ajax
     * @throws Exception
     */
    public static function load(): void
    {
        foreach (scandir(__DIR__ . '/assets') as $asset) {
            $extension = File::getExtension($asset);
            File::mkdir(Kernel::getPath('assets') . '/ajax/');

            if (in_array($extension, ['js', 'css'])) {
                $sourcePath = __DIR__ . '/assets/' . $asset;
                $destinationPath = Kernel::getPath('assets') . '/ajax/' . $asset;

                if (!file_exists($destinationPath) || filemtime($sourcePath) > filemtime($destinationPath)) {
                    DebugBar::addFrameworkMessage([
                        'source' => $sourcePath,
                        'destination' => $destinationPath
                    ], 'copy file');

                    File::copy($sourcePath, $destinationPath);
                }

                switch ($extension) {
                    case 'css':
                        View::addCssScript('/assets/ajax/' . $asset);

                        break;
                    case 'js':
                        View::addJsScript('/assets/ajax/' . $asset);

                        break;
                }
            }
        }
    }

}