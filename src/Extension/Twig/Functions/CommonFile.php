<?php

namespace Krzysztofzylka\MicroFramework\Extension\Twig\Functions;

use Krzysztofzylka\MicroFramework\Debug;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Twig\TwigFunction;

class CommonFile
{

    public function __construct(&$environment)
    {
        $formFunction = new TwigFunction('commonFile', function ($commonFileId, ?string $name = null) {
            Debug::startTime();

            $commonFile = (new \Krzysztofzylka\MicroFramework\Extension\CommonFile\CommonFile())->getCommonFile($commonFileId);

            if (!$commonFile['common_file']['is_public']) {
                if ($commonFile['common_file']['account_id'] !== Account::$accountId) {
                    return 'Brak pliku';
                }
            }

            echo '<a href="/common_file/download/' . $commonFile['common_file']['hash'] . '">'
                . ($name ?? ($commonFile['common_file']['name'] . '.' . $commonFile['common_file']['file_extension'] ?? ''))
                . '</a>';

            Debug::endTime('twig_commonFile_' . $commonFileId);
        });

        $environment->addFunction($formFunction);
    }

}