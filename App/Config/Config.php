<?php

namespace Config;

use Framework\Box;

class Config
{

    public function __construct()
    {
        /*
         * Set the full path to the docroot
         */
        defined('ROOT') or define('ROOT', realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR);

        // PATH
        defined('BASE_URL') or define(
            'BASE_URL',
            ((isset($_SERVER['HTTPS']) === true && $_SERVER['HTTPS'] === 'on' ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) === true && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'))
                ? 'https://' : 'http://') .
            $_SERVER["SERVER_NAME"] .
            (!empty($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], ['80, 443']) ? ':' . $_SERVER['SERVER_PORT'] : '')
        );

        /*
         * Application
         */
        if (empty(Box::getApplication())) {
            Box::setApplication(
                [
                    'title'               => 'Sloka',
                    'url'                 => BASE_URL,
                    'layout'              => 'default',
                    'timezone'            => 'Asia/Kolkata',
                    'UPLOAD_MAX_FILESIZE' => (100 * 1024 * 1024),

                // local|development|testing|production
                    'environment'         => 'local',
                    'email'               => 'email@example.com',
                    'language'            => 'en',

                    'sessionsTableName'   => 'sessions',
                ]
            );
        }

        /*
         * Framework
         */
        if (empty(Box::getFramework())) {
        Box::setFramework(
            [
                'controller'    => 'Index',
                'action'        => 'index',
                'service'       => 'Index',
                'operation'     => 'Index',
                'sessionPrefix' => 'sloka_',
            ]
        );
        }

        /*
         *  Environment
         */
        if (empty(Box::$env) && file_exists(__DIR__ . '/.env.php') === true) {
            Box::$env = include __DIR__ . '/.env.php';
        }

        /*
         * Include routes
         */
        if (empty(Box::$routes) && file_exists(__DIR__ . '/.routes.php') === true) {
            Box::$routes = include __DIR__ . '/.routes.php';
        }
    }
}
