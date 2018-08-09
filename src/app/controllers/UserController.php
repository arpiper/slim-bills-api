<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use App\models\User;

class UserController extends Controller {

    /*
    public function readUser (Request $req, Response $res, array $args) {
        $user = User::getUser($args['id']);
        $res = $res->withJson([
            'message' => 'User retrieved',
            'data' => [
                'user' => $user,
            ]
        ]);
        return $res;
    }

    public function readUsers (Reqeust $req, Resopnse $res, array $args) {
        $users = User::getUsers();
        $res = $res->withJson([
            'message' => 'Users retrieved',
            'data' => [
                'users' => $users,
            ]
        ]);
        return $res;
    }
    */

    public function createUser (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $userid = User::createUser($data);
        $res = $res->withJson([
            'message' => 'User created',
            'data' => [
                'userid' => $userid,
            ]
        ]);
        return $res;
    }

    public function updateUser (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $updated = User::updateUser($args['id'], $data);
        $res = $res->withJson([
            'message' => 'User retrieved',
            'data' => [
                'userid' => $args['id'],
                'updates' => $updated,
            ]
        ]);
        return $res;
    }
}
