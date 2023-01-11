<?php

namespace Krzysztofzylka\MicroFramework;

use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View {

    /**
     * Twig FileSystemLoader
     * @var FilesystemLoader
     */
    public static FilesystemLoader $filesystemLoader;

    /**
     * Twig Environment
     * @var Environment
     */
    public static Environment $environment;

    /**
     * Load view
     * @param string $name
     * @param array $variables
     * @return string
     * @throws ViewException
     */
    public function render(string $name, array $variables = []) : string {
        try {
            return self::$environment->render($name . '.twig', $variables);
        } catch (\Exception $exception) {
            throw new ViewException($exception->getMessage());
        }
    }

}