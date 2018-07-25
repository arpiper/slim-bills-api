<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use App\models\Utility;

class UtilityController {
    protected $container;

    public function __construct (Container $container) {
        $this->container = $container;
    }

    public function createUtility (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $util = Utility::createUtility($data);
        $res = $res->withJson([
            'message' => 'Utility created',
            'data' => [
                'utility' => $util,
            ],
        ]);
        return $res;
    }

    public function readUtility (Request $req, Response $res, array $args) {
        $util = Utility::getUtility($args['id']);
        $res = $res->withJson([
            'message' => '',
            'data' => [
                'utility' => $util,
            ],
        ]);
        return $res;
    } 

    public function readUtilities (Request $req, Response $res, array $args) {
        $utils = Utility::getUtilities();
        $res = $res->withJson([
            'message' => "$utils[count] persons found",
            'data' => $utils,
        ]);
        return $res;
    }

    public function updateUtility (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $updated = Utility::updateUtility($args['id'], $data);
        $message = 'Error occured updating Utility';
        if ($updated == 1) {
            $message = "$args[id] successfully updated";
        }
        $res = $res->withJson([
            'message' => $message,
            'data' => [
                'utility' => $args['id'],
            ],
        ]);
        return $res;
    }

    public function deleteUtility (Request $req, Response $res, array $args) {
        $res = $res->withJson([
            'message' => 'utility successfully deleted',
            'data' => [
                'truthiness' => 'placeholder function, nothing actually changed',
                'count' => 1,
            ],
        ]);
        return $res;
    }
}
