<?php

define('EXECUTION_PROFILER', ['time' => microtime(), 'memory' => memory_get_usage()]);

/*
* load composer autoloader
*/
if (file_exists('../vendor/autoload.php') === true) {
    include '../vendor/autoload.php';
} else {
    exit('Error: Composer installation is not valid. Please run "composer install" command. ');
}


/*
 * start
 */
$init = new \Framework\Initialize();

/*
 * Utility function for manual debugging
 */
function _x($data, $exit = true, $usePreTag = true)
{
    if ($usePreTag) {
        echo '<pre>';
    }

    print_r($data);
    if ($usePreTag) {
        echo '</pre>';
    }

    if ($exit) {
        exit();
    }

}
