<?php
namespace App\auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use \Firebase\JWT\JWT;
use \Tuupola\Base62;
use App\models\User;

class Auth {

    protected $secret;

    public function __construct ($secret) {
        $this->secret = $secret;
    }

    public function authorizeUser ($username, $password) {
        $user = User::getUser($username);
        // $username does not exist in the db.
        if (!$user) {
            return false;
        }

        // verify the given password
        if (password_verify($password, $user['password'])) {
            // user authorized 
            //$_SESSION['user'] = (string)$user['_id'];
            $token = $this->generateToken($username);
            return $token;
        }
        return false;
    }

    public function getToken (Request $req) {
        return $req->getCookieParam('token', false);
    }

    public function decodeToken (string $token) {
        try {
            $decoded = JWT::decode(
                $token,
                $this->secret, //'supersecretkeyyoushouldntcommit',
                (array) 'HS256'
            );
        } catch (\Firebase\JWT\ExpiredException $exception) {
            return $exception;
        }
        return $token;
    }

    public function checkToken () {
    }

    public function refreshToken ($token) {
        // set leeway to one hour.
        JWT::$leeway = 3600;
        $decoded = JWT::decode($token, $this->secret, ['HS256']);
        
        // update the iat, exp of the token.
        $decoded['iat'] = (new \DateTime())->getTimestamp();
        $decoded['exp'] = (new \DateTime("now +1 hour"))->getTimestamp();
        return [
            'token' => JWT::encode($decoded, $this->secret),
            'expires_in' => $decoded['exp']->getTimestamp()
        ];
    }

    public function generateToken ($username) {
        $now = new \DateTime();
        $exp = new \DateTime("now +1 hour");
        $jti = (new Base62)->encode(random_bytes(16));
        $payload = [
            'iat' => $now->getTimestamp(),
            'exp' => $exp->getTimestamp(),
            'jti' => $jti,
            'name' => $username,
        ];
        $secret = 'supersecretkeyyoushouldntcommit'; // need to set as env variable for production.
        $token = [
            'token' => JWT::encode($payload, $this->secret, 'HS256'),
            'expires_in' => $exp->getTimestamp(),
        ];
        return $token;
    }
}
