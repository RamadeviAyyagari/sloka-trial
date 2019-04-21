<?php

/**
 * Router - routing urls to closures and controllers -
 * modified from https://github.com/NoahBuscher/Macaw
 */

namespace Framework;

/**
 * Router class will load requested controller / closure based on url.
 */
class Router
{
    /**
     * Fallback for auto dispatching feature.
     *
     * @var boolean $fallback
     */
    public static $fallback = true;

    /**
     * If true - do not process other routes when match is found
     *
     * @var boolean $halts
     */
    public static $halts = true;

    /**
     * Array of routes
     *
     * @var array $routes
     */
    public static $routes = [];

    /**
     * Array of methods
     *
     * @var array $methods
     */
    public static $methods = [];

    /**
     * Array of callbacks
     *
     * @var array $callbacks
     */
    public static $callbacks = [];

    /**
     * Set an error callback
     *
     * @var null $errorCallback
     */
    public static $errorCallback;

    /**
     * Set route patterns
     *
     * @var array $patterns
     */
    public static $patterns = [
        ':any'    => '[^/]+',
        ':num'    => '-?[0-9]+',
        ':all'    => '.*',
        ':hex'    => '[[:xdigit:]]+',
        ':uuidV4' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}',
    ];

    /**
     * Defines a route with or without callback and method.
     *
     * @param string $method
     * @param
     *            array @params
     */
    public static function __callstatic($method, $params)
    {
        $uri      = dirname($_SERVER['PHP_SELF']) . '/' . $params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    public static function setAutomaticRoutes($automaticRouting)
    {
        self::$fallback = $automaticRouting;
    }

    /**
     * Add routes in bulk as an array
     *
     * @param array $routesList
     */
    public static function addCustomRoutes($routesList = [])
    {
        foreach ($routesList as $route => $callback) {
            array_push(self::$routes, dirname($_SERVER['PHP_SELF']) . '/' . $route);
            array_push(self::$methods, 'ANY');
            array_push(self::$callbacks, $callback);
        }
    }

    /**
     * Defines callback if route is not found.
     *
     * @param string $callback
     */
    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }

    /**
     * Don't load any further routes on match.
     *
     * @param boolean $flag
     */
    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    /**
     * Runs the callback for the given request.
     */
    public static function dispatch()
    {
        if ($_SERVER['REQUEST_URI'] == '') {
            return;
        }

        // get the custom routes
        self::addCustomRoutes(Box::getRoutes());

        $uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method   = $_SERVER['REQUEST_METHOD'];
        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace(['//', '/index.php/'], '/', self::$routes);
        $foundRoute   = false;

        // parse query parameters

        $query = '';
        if (strpos($uri, '&') > 0) {
            $query = substr($uri, (strpos($uri, '&') + 1));
            $uri   = substr($uri, 0, strpos($uri, '&'));
            $qArr  = explode('&', $query);
            foreach ($qArr as $q) {
                $qobj   = explode('=', $q);
                $qArr[] = [$qobj[0] => $qobj[1]];
                if (!isset($_GET[$qobj[0]])) {
                    $_GET[$qobj[0]] = $qobj[1];
                }
            }
        }

        // check if route is defined without regex
        if (in_array($uri, self::$routes)) {
            $routePos = array_keys(self::$routes, $uri);

            // foreach route position
            foreach ($routePos as $route) {
                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    $foundRoute = true;
                    // if route is not an object
                    if (!is_object(self::$callbacks[$route])) {
                        // call object controller and method
                        self::invokeObject(self::$callbacks[$route], null, null);
                        if (self::$halts) {
                            return;
                        }
                    } else {
                        // call closure
                        call_user_func(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    }
                }
            }
        } else {
            // check if defined with regex
            $pos = 0;

            // foreach routes
            foreach (self::$routes as $route) {
                $route = str_replace('//', '/', $route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
                        $foundRoute = true;

                        // remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        if (!is_object(self::$callbacks[$pos])) {
                            // call object controller and method
                            self::invokeObject(self::$callbacks[$pos], $matched, null);
                            if (self::$halts) {
                                return;
                            }
                        } else {
                            // call closure
                            call_user_func_array(self::$callbacks[$pos], $matched, null);
                            if (self::$halts) {
                                return;
                            }
                        }
                    }
                }

                $pos++;
            }
        }

        if (self::$fallback) {
            // call the auto dispatch method
            $foundRoute = self::autoDispatch();
        }

        // run the error callback if the route was not found
        if (!$foundRoute) {
            if (!self::$errorCallback) {
                self::$errorCallback = function () {
                    header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");

                    $data['title'] = '404';
                    $data['error'] = "Oops! Page not found..";

                    View::renderLayout('header', $data);
                    View::render('Error/404', $data);
                    View::renderLayout('footer', $data);
                };
            }

            if (!is_object(self::$errorCallback)) {
                // call object controller and method
                self::invokeObject(self::$errorCallback, null, 'No routes found.');
                if (self::$halts) {
                    return;
                }
            } else {
                call_user_func(self::$errorCallback);
                if (self::$halts) {
                    return;
                }
            }
        }
    }

    /**
     * autoDispatch by Volter9.
     *
     * Ability to call controllers in their controller/view/param way.
     */
    public static function autoDispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (defined('BASE_URL') && strpos($uri, BASE_URL) === 0) {
            $uri = substr($uri, strlen(BASE_URL));
        }

        $uri   = trim($uri, ' /');
        $uri   = ($amp = strpos($uri, '&')) !== false ? substr($uri, 0, $amp) : $uri;
        $parts = explode('/', $uri);

        /*
         * Check api routes
         */
        if ($parts[0] == 'api') {
            $folderName = array_shift($parts);
            $service    = array_shift($parts);
            $service    = !empty($service) ? $service : Box::getFramework()['service'];
            $service    = str_replace(' ', '_', ucwords(str_replace('-', ' ', $service)));

            if (file_exists(ROOT . "/Api/Services/{$service}Service.php")) {
                $service = "\Api\\Services\\{$service}Service";
            } else {
                $folderName = $service;
                $service    = array_shift($parts);
                $service    = !empty($service) ? $service : Box::getFramework()['service'];
                $service    = str_replace(' ', '', ucwords(str_replace('-', ' ', $service)));

                // check whether there is any module with that name
                if (file_exists(ROOT . "/Api/services/$folderName/{$service}Service.php")) {
                    $service = "Api\\services\\$folderName\\{$service}Service";
                } else {
                    $routeNotFound = true;
                }
            }

            $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
            $methodName    = array_shift($parts);
            $methodName    = str_replace(' ', '', ucwords(str_replace('-', ' ', $methodName)));
            $methodName    = $requestMethod . (!empty($methodName) ? $methodName : Box::getFramework()['operation']) . 'Operation';

            $args = !empty($parts) ? $parts : [];
            $c    = new $service();
            if (method_exists($c, $methodName)) {
                call_user_func_array([$c, $methodName], $args);
                // found method so stop
                return true;
            } else {
                $httpMethodNotAllowed = true;
            }

            if ($routeNotFound || $httpMethodNotAllowed) {
                $response = new \Framework\Response();
                if ($routeNotFound) {
                    $response->setHttpStatusCode('404', 'Not Found');
                } else if ($httpMethodNotAllowed) {
                    $response->setHttpStatusCode('405', 'Method Not Allowed');
                    $response->errorMessages = ['message' => 'Method Not Allowed'];
                }

                $response->dispatchJson();
            }
        } else {
            $controller = array_shift($parts);
            $controller = !empty($controller) ? $controller : Box::getFramework()['controller'];

            $controller = str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
            // Check for file in top Controllers folder
            if (file_exists(ROOT . "/App/Controllers/{$controller}Controller.php")) {
                $controller = "Controllers\\{$controller}Controller";
            } else {
                // check whether there is any module with that name
                if (file_exists(ROOT . "/App/Modules/{$controller}")) {
                    $moduleName = $controller;

                    $moduleController = array_shift($parts);
                    $moduleController = !empty($moduleController) ? $moduleController : Box::getFramework()['controller'];
                    $moduleController = str_replace(' ', '', ucwords(str_replace('-', ' ', $moduleController)));

                    if (file_exists(ROOT . "/App/Modules/$moduleName/Controllers/{$moduleController}Controller.php")) {
                        $controller = "Modules\\$moduleName\\Controllers\\{$moduleController}Controller";
                    } else {
                        return false;
                    }
                } else {
                    // check in sub folder beneath Contollers folder
                    $subFolderName = $controller;

                    $controller = array_shift($parts);
                    $controller = !empty($controller) ? $controller : Box::getFramework()['controller'];
                    $controller = str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));

                    if (file_exists(ROOT . "/App/Controllers/$subFolderName/{$controller}Controller.php")) {
                        $controller = "\Controllers\\$subFolderName\\{$controller}Controller";
                    } else {
                        return false;
                    }
                }
            }

            $method = array_shift($parts);
            $method = lcFirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $method))));
            $method = (!empty($method) ? $method : Box::getFramework()['action']) . 'Action';
            $args   = !empty($parts) ? $parts : [];
            $c      = new $controller();
            if (method_exists($c, $method)) {
                call_user_func_array([$c, $method], $args);
                // found method so stop
                return true;
            }

            return false;
        }
    }

    /**
     * Call object and instantiate.
     *
     * @param object $callback
     * @param array $matched
     *            array of matched parameters
     * @param string $msg
     */
    public static function invokeObject($callback, $matched = null, $msg = null)
    {
        $last = explode('/', $callback);
        $last = end($last);

        $segments = explode('@', $last);

        $controller = $segments[0];
        $method     = $segments[1];

        $controller = new $controller($msg);

        call_user_func_array([$controller, $method], $matched ? $matched : []);
    }

    public static function autoloadRegister($name)
    {
        $name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        if (file_exists('../' . $name . '.php')) {
            include_once('../' . $name . '.php');
        }
    }

}

spl_autoload_register(__NAMESPACE__ . '\Router::autoloadRegister');
