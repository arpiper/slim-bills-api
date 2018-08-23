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

        // set the defatul response data to succesfull login
        $json = [
            'message' => 'login successful',
            'status' => 200,
            'data' => [
                'expires_in' => '',
                'username' => $data['username'],
            ],
        ];

        // authorization failed. 
        if (!$auth) {
            // update the return json data to failure.
            $json['message'] = 'login failed';
            $json['status'] = 401;
            return $res->withJson($json);
        }
        
        // add secure flag for https only sending., add expiration
        // update the return json data with token expiration
        $json['data']['expires_in'] = $auth['expires_in'];
        $cookie = "token=$auth[token];path=/;httponly;";
        return $res->withAddedHeader('Set-Cookie', $cookie)
            ->withJson($json);
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
                $newToken = $this->auth->refreshToken($token);
                $data['data']['expires_in'] = $newToken['expires_in'];
                return $res->withHeader('Set-Cookie', "token=$newToken[token];path=/;httponly;")
                    ->$withJson($data);
            }
        }
        return $res->withJson($data);
    }

    /*
     * Refresh the token with a new expiration.
     */
    public function refreshToken(Request $req, Response $res, array $args) {
        // get the current token
        $token = $this->auth->getToken($req);
        // refresh the old token
        $newToken = $this->auth->refreshToken($token);
        $data = [
            'message' => 'token refreshed',
            'status' => 200,
            'data' => [
                'expires_in' => $newToken['expires_in'],
            ],
        ];
        // add secure flag for https only sending, add expiration
        $cookie = "token=$newToken[token];path=/;httponly;";
        return $res->withHeader('Set-Cookie', $cookie)
            ->withJson($data);
    }
}
