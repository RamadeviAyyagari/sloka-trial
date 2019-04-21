<?php

/*
 * Array for $box->config->routes
 */
return [
    '' => '\Controllers\IndexController@indexAction',
    'subpage/(:any)' => '\Controllers\IndexController@subPageAction',
];