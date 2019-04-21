<?php

namespace Framework;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var array */
    private $objects = [];

    /**
     * @param string $className
     * @return mixed
     */
    public function get($className)
    {
        if (isset($this->objects[$className])) {
            return $this->objects[$className];
        } else {
            return $this->objects[$className] = new $className();
        }
    }

    /**
     * @param string $className
     * @return bool
     */
    public function has($className)
    {
        return isset($this->objects[$className]);
    }

    /**
     * @param $className
     * @param $object
     */
    public function set($className, $object)
    {
        $this->objects[$className] = $object;

    }
}
