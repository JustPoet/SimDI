<?php
namespace SimDI\Tests;

class Car implements CarInterface
{
    protected $name = 'Benz';

    public function run()
    {
        return $this->name;
    }
}