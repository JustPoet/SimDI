<?php
namespace SimDI\Tests;

use PHPUnit\Framework\TestCase;
use SimDI\Container;

class ContainerTest extends TestCase
{
    protected $config = [
        CarInterface::class => Car::class
    ];

    /**
     * @var Container
     */
    protected $container;

    /**
     * @before
     */
    public function before()
    {
        $this->container = Container::getInstance($this->config);
    }

    public function testGet()
    {
        $driver = $this->container->get(Driver::class);
        $this->assertEquals('Benz', $driver->drive());
    }

    public function testSingleton()
    {
        $driver1 = $this->container->singleton(Driver::class);
        $driver2 = $this->container->singleton(Driver::class);
        $this->assertEquals($driver1, $driver2);
    }

    public function testArrayGet()
    {
        $driver = $this->container[Driver::class];
        $this->assertEquals('Benz', $driver->drive());
    }

    public function testPropGet()
    {
        $name = Driver::class;
        $driver = $this->container->$name;
        $this->assertEquals('Benz', $driver->drive());
    }
}