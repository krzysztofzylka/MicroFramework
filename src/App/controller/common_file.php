<?php

namespace Krzysztofzylka\MicroFramework\App\controller;

use krzysztofzylka\DatabaseManager\Exception\ConditionException;
use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Exception\TableException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use krzysztofzylka\SimpleLibraries\Library\File;

class common_file extends Controller
{

    /**
     * Download file from common file
     * @param mixed $hash
     * @return void
     * @throws ConditionException
     * @throws MicroFrameworkException
     * @throws NotFoundException
     * @throws SelectException
     * @throws TableException
     */
    public function download(mixed $hash): void
    {
        $hash = htmlspecialchars($hash);
        $hash = explode('.', $hash, 2)[0];

        $commonFile = $this->commonFile->getCommonFileByHash($hash);

        if (!$commonFile) {
            throw new NotFoundException();
        }

        if (!$commonFile['common_file']['is_public']) {
            if (Account::$accountId !== $commonFile['common_file']['account_id']) {
                throw new NotFoundException();
            }
        }

        try {
            $fileName = $commonFile['common_file']['name'] . '.' . $commonFile['common_file']['file_extension'];
            $contentType = File::getContentType($commonFile['common_file']['file_extension']);

            (new Table('common_file'))
                ->setId($commonFile['common_file']['id'])
                ->updateValue('download_count', $commonFile['common_file']['download_count'] + 1);

            header("Content-length: " . $commonFile['common_file']['file_size']);
            header('Content-Disposition: inline; filename="' . $fileName . '"');
            header('Content-type: ' . $contentType);
            readfile($commonFile['common_file']['file_path']);
        } catch (\Exception $exception) {
            $this->log('Błąd pobierania pliku', 'ERR', ['exception' => $exception->getMessage()]);

            throw new MicroFrameworkException('Błąd pobierania pliku');
        }

        exit;
    }

}