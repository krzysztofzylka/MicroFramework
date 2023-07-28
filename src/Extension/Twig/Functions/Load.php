<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\File;
use Twig\TwigFunction;

class Load
{

    public function __construct(&$environment)
    {
        $formFunction = new TwigFunction(
            'loadAsset',
            function ($assetPath) {
                $path = File::repairPath(Kernel::getPath('assets') . '/' . $assetPath);

                if (!file_exists($path)) {
                    return '';
                }

                $fileModifyTime = filemtime($path);

                return match (File::getExtension($path)) {
                    'js' => '<script src="/assets/' . $assetPath . '?' . $fileModifyTime . '"></script>',
                    'css' => '<link href="/assets/' . $assetPath . '?' . $fileModifyTime . '" rel="stylesheet">',
                    default => '',
                };

            }
        );

        $environment->addFunction($formFunction);
    }

}