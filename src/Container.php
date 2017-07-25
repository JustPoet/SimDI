<?php

namespace SimDI;

use ArrayAccess;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use SimDI\Exception\ContainerException;
use SimDI\Exception\NotFoundException;
use stdClass;

class Container implements ArrayAccess, ContainerInterface
{
    /**
     * @var Container
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var array
     */
    protected $contractsMapping;

    private function __construct($contractsMapping)
    {
        $this->contractsMapping = $contractsMapping;
    }

    private function __clone()
    {
    }

    /**
     * @param string $class
     * @param array  ...$params
     *
     * @return object
     */
    public function singleton(string $class, ...$params)
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        } else {
            $this->instances[$class] = $this->factory($class, $params);
        }

        return $this->instances[$class];
    }

    /**
     * @param string $class
     * @param array  ...$params
     *
     * @return object
     */
    public function get($class, ...$params)
    {
        return $this->factory($class, $params);
    }

    /**
     * @param string|ReflectionClass $class
     * @param array                  $params
     * @param array                  $cache
     *
     * @return object
     * @throws NotFoundException
     */
    protected function factory($class, $params = [], &$cache = [])
    {
        $class = new ReflectionClass($this->getClassName($class));

        if (!$class) {
            throw new NotFoundException('class dose not exist', -100);
        }

        if (!empty($params)) {
            return $class->newInstanceArgs($params);
        }

        $constructor = $class->getConstructor();

        $parameterClasses = $constructor ? $constructor->getParameters() : [];

        if (empty($parameterClasses)) {
            $obj = $class->newInstance();
            $obj->app = $this;
            return $obj;
        } else {
            foreach ($parameterClasses as $parameterClass) {
                $paramClass = $parameterClass->getClass();

                if (isset($cache[$paramClass->name])) {
                    $obj = $cache[$paramClass->name];
                } else {
                    $obj = $this->factory($paramClass, [], $cache);
                    $cache[$paramClass->name] = $obj;
                }

                $params[] = $obj;
            }

            $obj = $class->newInstanceArgs($params);
            $obj->app = $this;
            return $obj;
        }
    }

    /**
     * @param string|ReflectionClass $classObj
     *
     * @return mixed|string
     * @throws NotFoundException
     */
    protected function getClassName($classObj)
    {
        $className = '';
        if (is_string($classObj)) {
            $className = isset($this->contractsMapping[$classObj])
                ? $this->contractsMapping[$classObj] : $classObj;
        } elseif ($classObj->isInterface()) {
            $className = isset($this->contractsMapping[$classObj->name])
                ? $this->contractsMapping[$classObj->name] : '';
        } elseif ($classObj->isSubclassOf(stdClass::class)
            && !$classObj->isInterface()
        ) {
            $className = $classObj->name;
        } elseif ($classObj instanceof ReflectionClass) {
            $className = $classObj->name;
        }

        if (empty($className)) {
            throw new NotFoundException($className . ' dose not exist', -100);
        }

        return $className;
    }

    /**
     * @param array $contractsMapping
     *
     * @return Container
     */
    public static function getInstance($contractsMapping = [])
    {
        if (null === static::$instance) {
            static::$instance = new static($contractsMapping);
        }

        return static::$instance;
    }

    public function __get($class)
    {
        if (!isset($this->instances[$class])) {
            $this->instances[$class] = $this->factory($class);
        }

        return $this->instances[$class];
    }

    public function offsetExists($offset)
    {
        return isset($this->instances[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->instances[$offset])) {
            $this->instances[$offset] = $this->factory($offset);
        }

        return $this->instances[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new ContainerException('set object is forbidden!', -200);
    }

    public function offsetUnset($offset)
    {
        unset($this->instances[$offset]);
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function has($class)
    {
        return isset($this->instances[$class]);
    }
}