<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use Krzysztofzylka\MicroFramework\Debug;
use Twig\Environment;
use Twig\TwigFunction;

class JS
{

    public function __construct(Environment &$environment)
    {
        $formFunction = new TwigFunction('js', function ($params = []) use ($environment) {
            ob_start();
            Debug::startTime();

            $layout = $environment->getGlobals()['app']['layout'];
            $id = $environment->getGlobals()['app']['id'];
            $controller = $environment->getGlobals()['app']['controller']->name;
            $class = ($layout === 'dialogbox' ? 'dialog_' : '') . $id;
            $name = $environment->getGlobals()['app']['name'];

            echo '<script>
                $(document).ready(function () {
                    jsPath = "/public_files/js/' . $controller . '/' . $name . '";
                    $self = $("#' . $class . '");
                    params = \'' . json_encode($params) . '\';
                    
                    (function($self, params, jsPath){
                        $.getScript(jsPath, function() {
                            main($self, JSON.parse(params));
                        }).fail(function() {
                            console.debug("ERR");
                        });
                    })($self, params, jsPath);
                });
            </script>';

            echo '<script src="/public_files/js/' . $controller . '/' . $name . '"></script>';

            Debug::endTime('twig_js');

            return ob_get_clean();
        });

        $environment->addFunction($formFunction);
    }

}