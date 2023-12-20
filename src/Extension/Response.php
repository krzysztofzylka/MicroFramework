<?php

namespace Krzysztofzylka\MicroFramework\Extension;

use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;

/**
 * Response extension
 */
class Response
{

    /**
     * Response JSON data
     * @param array $data
     * @param int|null $statusCode
     * @return never
     */
    public function json(array $data, ?int $statusCode = null): never
    {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!is_null($statusCode)) {
            http_response_code($statusCode);
        }

        die(json_encode($data));
    }

    /**
     * Reads the contents of a file and sends it as a response.
     * @param string $path The path of the file.
     * @param string|null $contentType The optional content type of the response. If not provided, it will be determined based on the file extension.
     * @return never
     * @throws NotFoundException If the file does not exist.
     */
    public function fileContents(string $path, ?string $contentType = null): never
    {
        if (!file_exists($path)) {
            throw new NotFoundException();
        }

        $fileName = basename($path);
        $contentType = $contentType ?? File::getContentType(File::getExtension($path));

        header("Content-length: " . filesize($path));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-type: ' . $contentType);
        die(readfile($path));
    }

}