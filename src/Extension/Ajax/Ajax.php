<?php

namespace Krzysztofzylka\MicroFramework\Extension\Ajax;

use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;

class Ajax
{

    public static function load()
    {
        View::addJsScript('https://code.jquery.com/jquery-3.7.1.min.js');
        View::addJsScript('https://code.jquery.com/ui/1.13.1/jquery-ui.min.js');
        View::addCssScript('https://code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css');
        View::addJsScript('https://cdn.jsdelivr.net/npm/vanillatoasts@1.4.0/vanillatoasts.js');
        View::addCssScript('https://cdn.jsdelivr.net/npm/vanillatoasts@1.4.0/vanillatoasts.css');

        foreach (scandir(__DIR__ . '/assets') as $asset) {
            $extension = File::getExtension($asset);
            File::mkdir(Kernel::getPath('assets') . '/ajax/');

            if (in_array($extension, ['js', 'css'])) {
                $sourcePath = __DIR__ . '/assets/' . $asset;
                $destinationPath = Kernel::getPath('assets') . '/ajax/' . $asset;

//                if (!file_exists($destinationPath) || filemtime($sourcePath) < filemtime($destinationPath)) {
                    File::copy($sourcePath, $destinationPath);
//                }

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