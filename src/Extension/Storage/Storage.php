<?php

namespace Krzysztofzylka\MicroFramework\Extension\Storage;

use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\File;

class Storage {

    /**
     * Custom storage directory
     * @var string
     */
    private string $directory = '';

    /**
     * Actual file path
     * @var string
     */
    private string $path;

    /**
     * Storage file name
     * @var string
     */
    private string $fileName;

    /**
     * Isolator directory
     * @var string
     */
    private string $isolatorDirectory = '';

    /**
     * Initialize extension
     * @throws MicroFrameworkException
     */
    public function __construct()
    {
        $this->generatePath();
    }

    /**
     * Change directory path in storage
     * @param string $directoryPath
     * @return Storage
     * @throws MicroFrameworkException
     */
    public function setDirectory(string $directoryPath): Storage
    {
        if (!str_ends_with($directoryPath, '/')) {
            $directoryPath .= '/';
        }

        $this->directory = $directoryPath;

        return $this;
    }

    /**
     * Set file name
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): Storage
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Write file content
     * @param string $content
     * @return bool
     * @throws MicroFrameworkException
     */
    public function write(string $content): bool
    {
        if (!isset($this->fileName)) {
            throw new MicroFrameworkException('Storage file name is not defined');
        }

        $this->generatePath();

        try {
            return file_put_contents($this->getFilePath(), $content) !== false;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Read storage file
     * @return string|false
     */
    public function read(): string|false
    {
        return file_get_contents($this->getFilePath());
    }

    /**
     * Get storage modified date
     * @return int|false
     */
    public function getModifiedDate(): int|false
    {
        return filemtime($this->getFilePath());
    }

    /**
     * Delete storage file
     * @return bool
     */
    public function delete(): bool
    {
        return File::unlink($this->getFilePath());
    }

    /**
     * Set isolator directory
     * @param string $isolatorDirectory
     * @return Storage
     */
    public function setIsolatorDirectory(string $isolatorDirectory): Storage
    {
        if (!str_ends_with($isolatorDirectory, '/')) {
            $isolatorDirectory .= '/';
        }

        $this->isolatorDirectory = $isolatorDirectory;

        return $this;
    }

    /**
     * Set isolator directory by account
     * @return Storage
     */
    public function setAccountIsolator(?int $accountId = null): Storage
    {
        $this->isolatorDirectory = 'account_' . ($accountId ?? Account::$accountId ?? 0) . '/';

        return $this;
    }

    /**
     * Generate storage directory path
     * @return void
     * @throws MicroFrameworkException
     */
    private function generatePath(): void
    {
        try {
            $this->path = realpath(Kernel::getPath('storage')) . '/' . $this->directory;

            if (!empty($this->isolatorDirectory)) {
                $this->path .= $this->isolatorDirectory;
            }

            if (!empty($this->directory)) {
                File::mkdir($this->path);
            }
        } catch (\Exception) {
            throw new MicroFrameworkException('Failed create storage directory');
        }
    }

    /**
     * Get storage file path
     * @return string
     */
    private function getFilePath(): string
    {
        return $this->path . '/' . $this->fileName;
    }

}