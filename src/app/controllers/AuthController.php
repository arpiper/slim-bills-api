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
        
        // add secure flag for https only sending.
        return $res->withAddedHeader('Set-Cookie', "token=$auth[token];path=/;httponly")
            ->withJson([
                'message' => 'login successful',
                'data' => [
                    'username' => $data['username'],
                ],
            ]);
    }

    public function logout(Request $req, Response $res, array $args) {
        $cookie = 'token=deleted;path=/;httponly;';
        $res = $res->withAddedHeader('Set-Cookie', $cookie)
            ->withJson(['message' => 'successfully logged out']);
        return $res;
    }

    public function checkAuth(Request $req, Response $res, array $args) {
        return $res;
    }
}
