<?php

namespace SimDI\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements
    ContainerExceptionInterface
{

}