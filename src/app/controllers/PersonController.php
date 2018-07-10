<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class PersonController {
    protected $container;

    public function __construct (Container $container) {
        $this->container = $container;
    }

    public function createPerson (Request $req, Response $res, array $args) {
    }

    public function readPerson (Request $req, Response $res, array $args) {
    } 

    public function readPersons (Request $res, Response $res, array $args) {
    }

    public function updatePerson (Request $req, Response $res, array $args) {
    }

    public function deletePerson (Request $req, Response $res, array $args) {
    }
}
