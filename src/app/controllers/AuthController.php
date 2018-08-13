<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use App\auth\Auth;

class AuthController extends Controller {

    public function login(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $auth = Auth::authorizeUser(
            $data['username'],
            $data['password']
        );
        
        if (!$auth) {
            return $res->withJson(['login' => 'failed']);
        }

        return $res->withJson(['jwt' => $auth]);
    }

    public function logout(Request $req, Response $res, array $args) {
        return $res;
    }
}
