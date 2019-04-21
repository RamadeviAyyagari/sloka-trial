<?php

namespace Framework;

class Box
{
    /** @var array */
    public static $config = [];

    /** @var array */
    public static $env = [];

    /** @var array */
    public static $application = [];

    /** @var array */
    public static $framework = [];

    /** @var array */
    public static $routes = [];

    /** @var array */
    public static $data = [];

    /**
     * @param $key
     * @param $value
     */
    public function setConfig($key, $value): void
    {
        self::$config[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getConfig($key)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        } else {
            return null;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function setEnv($name, $value)
    {
        self::$env[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getEnv($name)
    {
        if (isset(self::$env[$name])) {
            return self::$env[$name];
        } else {
            return null;
        }
    }

    /**
     * @param array $application
     */
    public static function setApplication(array $application): void
    {
        self::$application = $application;
    }

    /**
     * @return array
     */
    public static function getApplication(): array
    {
        return self::$application;
    }

    /**
     * @param array $framework
     */
    public static function setFramework(array $framework): void
    {
        self::$framework = $framework;
    }

    /**
     * @return array
     */
    public static function getFramework(): array
    {
        return self::$framework;
    }

    /**
     * @param $pattern
     * @param $callback
     */
    public static function setRoute($pattern, $callback)
    {
        self::$routes[$pattern] = $callback;
    }

    public static function getRoute($pattern)
    {
        return self::$routes[$pattern];
    }

    /**
     * @param array $routes
     */
    public static function setRoutes(array $routes): void
    {
        self::$routes = $routes;
    }

    /**
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * @param array $data
     */
    public static function setData(array $data): void
    {
        self::$data = array_merge(self::$data, $data);
    }

    /**
     * @return array
     */
    public static function getData(): array
    {
        return self::$data;
    }

}
