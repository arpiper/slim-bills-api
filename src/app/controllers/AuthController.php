<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use App\auth\Auth;

class AuthController extends Controller {

    protected $auth;

    public function __construct (container $con) {
        $this->auth = $con->auth;
    }

    public function login(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $auth = $this->auth->authorizeUser(
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
                'status' => 200,
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

    /*
     * Check the JWT in the token cookie. Refresh if necessary.
     */
    public function checkAuth(Request $req, Response $res, array $args) {
        $data = [
            'message' => 'authentication check: not authorized',
            'status' => 401,
            'data' => [
                'auth' => '',
            ]
        ];
        $token = $this->auth->getToken($req);
        if ($token) {
            try {
                $decoded = $this->auth->decodeToken($token);
                $data['data']['auth'] = true;
                $data['status'] = 200;
            } catch (\Firebase\JWT\ExpiredException $exception) {
                return $res->withJson($data);
            }
        }
        return $res->withJson($data);
    }
}
