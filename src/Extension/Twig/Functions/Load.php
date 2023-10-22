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
                return match (File::getExtension($assetPath)) {
                    'js' => '<script src="/public_files/assets/' . $assetPath . '"></script>',
                    'css' => '<link href="/public_files/assets/' . $assetPath . '" rel="stylesheet">',
                    default => '',
                };

            }
        );

        $environment->addFunction($formFunction);
    }

}