<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class LoginController extends Controller {

    public function login(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        
    }
}
