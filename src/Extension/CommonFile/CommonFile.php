<?php

namespace Krzysztofzylka\MicroFramework\Extension\CommonFile;

use Exception;
use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\Exception\ConditionException;
use krzysztofzylka\DatabaseManager\Exception\DeleteException;
use krzysztofzylka\DatabaseManager\Exception\InsertException;
use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Exception\TableException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Storage\Storage;
use krzysztofzylka\SimpleLibraries\Library\File;
use krzysztofzylka\SimpleLibraries\Library\Generator;
use krzysztofzylka\SimpleLibraries\Library\Request;

/**
 * Common file
 * @package Extension\Cron
 */
class CommonFile
{

    /**
     * Account isolator
     * @var int
     */
    public int $accountIsolator;

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
        if (!$_ENV['database_enabled']) {
            return;
        }

        if (!isset($this->storage)) {
            $this->initStorage();
        }

        if (!isset($this->tableInstance)) {
            $this->tableInstance = new Table('common_file');
        }

        if (!isset($this->accountIsolator)) {
            $this->accountIsolator = Account::$accountId ?? -1;
        }
    }

    /**
     * Upload file
     * @throws NotFoundException
     * @throws InsertException
     * @throws Exception
     */
    public function uploadFile(
        string $path,
        ?bool  $isTemp = false,
        ?int   $tempTime = 86400,
        ?bool  $isPublic = false
    ): false|int
    {
        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        $accountId = $this->accountIsolator ?? Account::$accountId ?? -1;
        $fileExtension = File::getExtension($path);
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        $fileSize = filesize($path);

        $this->storage->setFileName($this->generateFileName());
        $this->storage->generatePath();

        $filePath = $this->storage->getFilePath();

        copy($path, $filePath);

        $insertData = [
            'account_id' => $accountId,
            'name' => $fileName,
            'file_path' => $filePath,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'is_public' => (int)$isPublic,
            'is_temp' => (int)$isTemp,
            'date_temp' => date('Y-m-d H:i:s', time() + $tempTime),
            'hash' => $this->generateHash()
        ];

        if (!$this->tableInstance->insert($insertData)) {
            return false;
        }

        return $this->tableInstance->getId();
    }

    /**
     * Zapis tekstu do pliku
     * @param string $text
     * @param string $fileName
     * @param bool|null $isTemp
     * @param int|null $tempTime
     * @param bool|null $isPublic
     * @return false|int|null
     * @throws InsertException
     * @throws MicroFrameworkException
     * @throws Exception
     */
    public function write(
        string $text,
        string $fileName,
        ?bool  $isTemp = false,
        ?int   $tempTime = 86400,
        ?bool  $isPublic = false
    ): false|int|null
    {
        $accountId = $this->accountIsolator ?? Account::$accountId ?? -1;
        $fileExtension = File::getExtension($fileName);
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        $this->storage->setFileName($this->generateFileName());
        $this->storage->generatePath();
        $this->storage->write($text);

        $filePath = $this->storage->getFilePath();
        $fileSize = filesize($filePath);

        $insertData = [
            'account_id' => $accountId,
            'name' => $fileName,
            'file_path' => $filePath,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'is_public' => (int)$isPublic,
            'is_temp' => (int)$isTemp,
            'date_temp' => date('Y-m-d H:i:s', time() + $tempTime),
            'hash' => $this->generateHash()
        ];

        if (!$this->tableInstance->insert($insertData)) {
            return false;
        }

        return $this->tableInstance->getId();
    }

    /**
     * Delete common file
     * @param int $commonFileId
     * @return bool
     * @throws ConditionException
     * @throws SelectException
     * @throws TableException
     * @throws DeleteException
     */
    public function delete(int $commonFileId): bool
    {
        $commonFile = $this->getCommonFile($commonFileId);

        if (!$commonFile) {
            return false;
        }

        File::unlink($commonFile['common_file']['file_path']);
        return $this->tableInstance->delete($commonFileId);
    }

    /**
     * Get common file
     * @param int $commonFileId
     * @return array
     * @throws ConditionException
     * @throws SelectException
     * @throws TableException
     */
    public function getCommonFile(int $commonFileId): array
    {
        return $this->tableInstance->find([
            'common_file.id' => $commonFileId,
            'OR' => [
                new Condition('common_file.account_id', '=', $this->accountIsolator),
                new Condition('common_file.account_id', '=', -1)
            ]
        ]);
    }

    /**
     * Get common file
     * @param string $hash
     * @return array
     * @throws ConditionException
     * @throws SelectException
     * @throws TableException
     */
    public function getCommonFileByHash(string $hash): array
    {
        return $this->tableInstance->find([
            'common_file.hash' => $hash,
            'OR' => [
                new Condition('common_file.account_id', '=', $this->accountIsolator),
                new Condition('common_file.account_id', '=', -1)
            ]
        ]);
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

    /**
     * Set isolator
     * @param int $accountId
     * @return void
     */
    public function setIsolator(int $accountId): void
    {
        $this->accountIsolator = $accountId;
    }

    /**
     * Upload file from form input
     * @param string $inputFileName
     * @param bool $isPublic
     * @param bool $isTemp
     * @param int $tempTime
     * @return false|int
     * @throws InsertException
     * @throws MicroFrameworkException
     * @throws Exception
     */
    public function uploadFromForm(
        string $inputFileName,
        bool $isPublic = false,
        bool $isTemp = false,
        int $tempTime = 86400
    ): false|int
    {
        $files = Request::getFiles();

        if (!isset($files[$inputFileName])) {
            return false;
        }

        $fileExtension = File::getExtension($files[$inputFileName]['name']);
        $fileName = pathinfo($files[$inputFileName]['name'], PATHINFO_FILENAME);

        $this->storage->setFileName($this->generateFileName());
        $this->storage->generatePath();

        $filePath = $this->storage->getFilePath();
        move_uploaded_file($files[$inputFileName]['tmp_name'], $filePath);
        $fileSize = filesize($filePath);

        $insertData = [
            'account_id' => $this->accountIsolator ?? Account::$accountId ?? -1,
            'name' => $fileName,
            'file_path' => $filePath,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'is_public' => (int)$isPublic,
            'is_temp' => (int)$isTemp,
            'date_temp' => date('Y-m-d H:i:s', time() + $tempTime),
            'hash' => $this->generateHash()
        ];

        if (!$this->tableInstance->insert($insertData)) {
            return false;
        }

        return $this->tableInstance->getId();
    }

    /**
     * Generate file name
     * @return string
     * @throws Exception
     */
    private function generateFileName(): string
    {
        return Generator::uniqId(50);
    }

    /**
     * Generate hash
     * @return string
     * @throws Exception
     */
    private function generateHash(): string
    {
        return Generator::uniqId(100);
    }

}