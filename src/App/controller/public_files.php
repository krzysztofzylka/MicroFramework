<?php

namespace Krzysztofzylka\MicroFramework\App\controller;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\File;

class public_files extends Controller
{

    /**
     * @param string ...$assetPath
     * @return void
     * @throws NotFoundException
     */
    public function assets(string ...$assetPath): void {
        $assetPath = implode('/', $assetPath);
        $assetPath = File::repairPath($assetPath);
        $assetPath = str_replace(['../', '//', '\\'], '', $assetPath);
        $assetPath = htmlspecialchars($assetPath);
        $extension = File::getExtension($assetPath);

        if (!in_array($extension, ['js', 'jsx', 'css'])) {
            throw new NotFoundException();
        }

        $path = realpath(__DIR__ . '/../../Resources/assets') . '/' . $assetPath;

        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        header("Content-length: " . filesize($path));
        header('Content-Disposition: inline; filename="' . basename($path) . '"');
        header('Content-type: ' . $this->getContentType($extension));
        readfile($path);
        exit;
    }

    /**
     * Download js file from view
     * @param string $controller
     * @param string $method
     * @return void
     * @throws NotFoundException
     */
    public function js(string $controller, string $method): void
    {
        $this->layout = 'none';
        $path = Kernel::getPath('view') . '/' . htmlspecialchars($controller) . '/' . htmlspecialchars($method) . '.js';

        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        $fileName = basename($path);

        header("Content-length: " . filesize($path));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-type: text/javascript');
        readfile($path);
        exit;
    }

    /**
     * Get content type
     * @param string $fileExtension
     * @return false|string
     */
    private function getContentType(string $fileExtension): false|string
    {
        $images = ['gif', 'png', 'webp', 'bmp', 'avif'];
        $text = ['css', 'csv'];
        $video = ['mp4', 'webm'];
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
            default => false
        };
    }

}