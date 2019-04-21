<?php

namespace Framework;

use Config\Config;

class Initialize
{
    public function __construct(Config $config = null)
    {
        Profiler::start(EXECUTION_PROFILER);

        defined('ROOT') or define('ROOT', realpath(__DIR__ . '/../'));
        /*
         * Turn on output buffering.
         */
        ob_start();

        /*
         * check the configuration file presence
         */
        if (file_exists(ROOT . '/App/Config/Config.php')) {
            /*
             * Populate config variables
             */
            if ($config == null) {
                new \Config\Config();
            }

            /*
             * Error reporting
             */
            error_reporting(($box->config->env['errorReporting'] ?? E_ALL));
            set_exception_handler('Framework\Logger::ExceptionHandler');
            set_error_handler('Framework\Logger::ErrorHandler');

            /*
             * Application specific initializations
             */

            if ($config == null) {
                new \Helpers\Initialize();
            }

            /*
             * Router
             */
            Router::setAutomaticRoutes(true);
            Router::dispatch();
        } else {
            exit('Error: Could not find ' . ROOT . '/App/Config/Config.php');
        }

        Profiler::stop();
    }
}
