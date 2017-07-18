<?php
namespace SimDI\Tests;

class Driver
{
    protected $car;

    public function __construct(CarInterface $car)
    {
        $this->car = $car;
    }

    public function drive()
    {
        return $this->car->run();
    }
}