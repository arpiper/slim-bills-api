<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class AuthController extends Controller {

    public function login(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $auth = $this->container->auth->attemptLogin(
            $data['username'],
            $data['password']
        );
        
        if (!$auth) {
            return $response->withJson({'login': 'failed'});
        }

        return $response->withJson({'hello': "$data[username]"});
    }
}
