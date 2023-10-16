<?php

namespace Krzysztofzylka\MicroFramework\Extension\CommonFile;

use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Storage\Storage;

/**
 * Common file
 * @package Extension\Cron
 */
class CommonFile
{

    /**
     * Storage object
     * @var Storage
     */
    private Storage $storage;

    /**
     * Table instance
     * @var Table
     */
    private Table $tableInstance;

    /**
     * Initialize object
     * @throws MicroFrameworkException
     */
    public function __construct()
    {
        if (!isset($this->storage)) {
            $this->initStorage();
        }

        if (!isset($this->tableInstance)) {
            $this->tableInstance = new Table('common_file');
        }
    }

    public function uploadFile(
        string $path,
        ?string $fileName = null,
        ?bool $isTemp = false,
        ?int $tempTime = 86400,
        ?bool $isPublic = false
    ): bool
    {
        $accountId = Account::$accountId ?? -1;
        $filePath = '';
        $fileExtension = '';
        $fileSize = filesize($filePath);

        $this->tableInstance->insert([
            'account_id' => $accountId,
            'name' => $fileName,
            'file_path' => $filePath,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'is_public' => $isPublic,
            'is_temp' => $isTemp,
            'date_temp' => date('Y-m-d H:i:s', time() + $tempTime)
        ]);

        return true;
    }

    /**
     * Initialize storage object
     * @return void
     * @throws MicroFrameworkException
     */
    private function initStorage(): void
    {
        $this->storage = (new Storage())
            ->setDirectory('common_file')
            ->setAccountIsolator(Account::$accountId)
            ->lock();
    }

}