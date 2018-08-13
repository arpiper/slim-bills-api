<?php
namespace App\auth;

use \Firebase\JWT\JWT;
use \Tuupola\Base62;
use App\models\User;

class Auth {

    public static function authorizeUser ($username, $password) {
        $user = User::getUser($username);
        // $username does not exist in the db.
        if (!$user) {
            return false;
        }

        // verify the given password
        if (password_verify($password, $user['password'])) {
            // user authorized 
            //$_SESSION['user'] = (string)$user['_id'];
            $token = self::generateToken($username);
            return $token;
        }
        return false;
    }

    public static function checkToken () {
    }

    public static function refreshToken () {
    }

    public static function generateToken ($username) {
        $now = new \DateTime();
        $exp = new \DateTime("+15 minutes");
        $jti = (new Base62)->encode(random_bytes(16));
        $payload = [
            'iat' => $now->getTimestamp(),
            'exp' => $exp->getTimestamp(),
            'jti' => $jti,
            'name' => $username,
        ];
        $secret = 'supersecretkeyyoushouldntcommit'; // need to set as env variable for production.
        $token = JWT::encode($payload, $secret, 'HS256');
        return $token;
    }
}
