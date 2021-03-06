<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use App\models\Person;

class PersonController {
    protected $container;

    public function __construct (Container $container) {
        $this->container = $container;
    }

    public function createPerson (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $personid = Person::createPerson($data);
        $res = $res->withJson([
            'message' => 'Person cretaed',
            'data' => [
                'personid' => $personid,
            ],
        ]);
        return $res;
    }

    public function readPerson (Request $req, Response $res, array $args) {
        $person = Person::getPerson($args['id']);
        $res = $res->withJson([
            'message' => '',
            'data' => [
                'person' => $person,
            ],
        ]);
        return $res;
    } 

    public function readPersons (Request $req, Response $res, array $args) {
        $persons = Person::getPersons();
        $res = $res->withJson([
            'message' => "$persons[count] persons found",
            'data' => $persons,
        ]);
        return $res;
    }

    public function updatePerson (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $updated = Person::updatePerson($args['id'], $data);
        $message = 'Error occurred updating person';
        if ($updated == 1) {
            $message = "$args[id] successfully updated";
        }
        $res = $res->withJson([
            'message' => $message,
            'data' => [
                'person' => $args['id'],
            ]
        ]);
        return $res;
    }

    public function deletePerson (Request $req, Response $res, array $args) {
        $res = $res->withJson([
            'message' => 'person successfully deleted',
            'data' => [
                'truthiness' => 'placeholder function, nothing actually changed',
                'count' => 1,
            ],
        ]);
        return $res;
    }
}
