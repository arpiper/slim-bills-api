<?php
namespace App\middleware;

use Psr\Container\ContainerInterface as Container;

class Middleware {
    
    protected $container;

    public function __construct (Container $container) {
        $this->container = $container;
    }
}
