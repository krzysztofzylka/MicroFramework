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
            $contentType = $this->getContentType($commonFile['common_file']['file_extension']);

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

    /**
     * Get content type
     * @param string $fileExtension
     * @return false|string
     */
    private function getContentType(string $fileExtension): false|string
    {
        $images = ['gif', 'png', 'webp', 'bmp', 'avif', 'pjpeg'];
        $text = ['css', 'csv'];
        $video = ['mp4', 'webm', 'jpm'];
        $application = ['zip', 'xml', 'rtf', 'pdf', 'json'];
        $font = ['woff2', 'woff', 'ttf', 'otf'];
        $audio = ['wav', 'pus', 'aac'];

        if (in_array($fileExtension, $images)) {
            return 'image/' . $fileExtension;
        } elseif (in_array($fileExtension, $text)) {
            return 'text/' . $fileExtension;
        } elseif (in_array($fileExtension, $video)) {
            return 'video/' . $fileExtension;
        } elseif (in_array($fileExtension, $application)) {
            return 'application/' . $fileExtension;
        } elseif (in_array($fileExtension, $font)) {
            return 'font/' . $fileExtension;
        } elseif (in_array($fileExtension, $audio)) {
            return 'audio/' . $fileExtension;
        }

        return match ($fileExtension) {
            'jpeg', 'jpg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'text' => 'text/plain',
            'doc' => 'application/msword',
            'js', 'mjs' => 'text/javascript',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            '7z' => 'application/x-7z-compressed',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xul' => 'application/vnd.mozilla.xul+xml',
            '3gp' => 'video/3gpp',
            '3g2' => 'video/3gpp2',
            'xhtml' => 'application/xhtml+xml',
            'xls' => 'application/vnd.ms-excel',
            'vsd' => 'application/vnd.visio',
            'rar' => 'application/vnd.rar',
            'ts' => 'video/mp2t',
            'tif', 'tiff' => 'image/tiff',
            'tar' => 'application/x-tar',
            'sh' => 'application/x-sh',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppt' => 'application/vnd.ms-powerpoint',
            'php' => 'application/x-httpd-php',
            'ogx' => 'application/ogg',
            'ogv' => 'video/ogg',
            'mp3' => 'audio/mpeg',
            'mid', 'midi' => 'audio/midi',
            'jsonld' => 'application/ld+json',
            'jar' => 'application/java-archive',
            'ics' => 'text/calendar',
            'ico' => 'image/vnd.microsoft.icon',
            'htm', 'html' => 'text/html',
            'gz' => 'application/gzip',
            'epub' => 'application/epub+zip',
            'eot' => 'application/vnd.ms-fontobject',
            'csh' => 'application/x-csh',
            'cda' => 'application/x-cdf',
            'bz2' => 'application/x-bzip2',
            'bz' => 'application/x-bzip',
            'bin' => 'application/octet-stream',
            'awz' => 'application/vnd.amazon.ebook',
            'avi' => 'video/x-msvideo',
            'arc' => 'application/x-freearc',
            'abw' => 'application/x-abiword',
            'x3d' => 'application/vnd.hzn-3d-crossword',
            'mseq' => 'application/vnd.mseq',
            'pwn' => 'application/vnd.3m.post-it-notes',
            'ace' => 'application/x-ace-compressed',
            'dir' => 'application/x-director',
            'apk' => 'application/vnd.android.package-archive',
            'aiff' => 'audio/x-aiff',
            'atom' => 'application/atom+xml',
            'torrent' => 'application/x-bittorrent',
            'c' => 'text/x-c',
            'deb' => 'application/x-debian-package',
            'dts' => 'audio/vnd.dts',
            'flv' => 'video/x-flv',
            'f4v' => 'video/x-f4v',
            'cer' => 'application/pkix-cert',
            'java' => 'text/x-java-source',
            'jsx' => 'text/jsx',
            'kml' => 'application/vnd.google-earth.kml+xml',
            'kmz' => 'application/vnd.google-earth.kmz',
            'm4a' => 'audio/x-m4a',
            'm4v' => 'video/x-m4v',
            'm4p' => 'application/mp4',
            'm4u' => 'video/vnd.mpegurl',
            'm3u8' => 'application/vnd.apple.mpegurl',
            'm3u' => 'audio/x-mpegurl',
            'latex' => 'application/x-latex',
            'kwd' => 'application/vnd.kde.kword',
            'kon' => 'application/vnd.kde.kontour',
            'ser' => 'application/java-serialized-object',
            'karbon' => 'application/vnd.kde.karbon',
            'kfo' => 'application/vnd.kde.kformula',
            'flw' => 'application/vnd.kde.kivio',
            default => false
        };
    }

}