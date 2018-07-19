<?php
namespace App\auth;

class Auth {

    public function attemptLogin ($username, $password) {
        // grab user
        $user = '';
        if (!$user) {
            return false;
        }
        if (password_verify($password, $user->password)) {
            $_SESSION['user'] = $user->id;
            return true;
        }
        return false;
    }
}
