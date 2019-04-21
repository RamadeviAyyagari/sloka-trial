<?php

/**
 * View - load template pages
 */

namespace Framework;

class View
{

    /** @var array $headers */
    private static $headers = [];

    public static function render($path, $error = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }

        if (file_exists(ROOT . "/App/Modules/$path.phtml")) {
            include ROOT . "/App/Modules/$path.phtml";
        } else if (file_exists(ROOT . "/App/views/$path.phtml")) {
            require ROOT . "/App/views/$path.phtml";
        } else {
            throw new \Exception('View not found - ' . $path);
        }

        http_response_code(200);
    }

    public static function renderDefault($path, $layout = null)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }

        // set default layout
        if (empty($layout)) {
            $layout = Box::getApplication()['layout'];
        }

        // render header & footer automatically
        self::renderLayout('header', $layout);

        // actual view
        if (file_exists(ROOT . "/App/Modules/$path.phtml")) {
            // render module view
            require ROOT . "/App/Modules/$path.phtml";
        } else if (file_exists(ROOT . "/App/views/$path.phtml")) {
            require ROOT . "/App/views/$path.phtml";
        } else {
            throw new \Exception('View not found - ' . $path);
        }

        // footer
        self::renderLayout('footer', $layout);

        http_response_code(200);
    }

    public static function renderLayout($path, $layout = null)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }

        // set default layout
        if (empty($layout)) {
            $layout = Box::getApplication()['layout'];
        }

        if (file_exists(ROOT . "/App/Layouts/$layout/$path.phtml")) {
            include(ROOT . "/App/Layouts/$layout/$path.phtml");
        } else {
            throw new \Exception('Layout not found - ' . ROOT . "/App/Layouts/$layout/$path.phtml");
        }

        http_response_code(200);
    }

    public function addHeader($header)
    {
        self::$headers[] = $header;
    }

    public function addHeaders($headers = [])
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }

}
