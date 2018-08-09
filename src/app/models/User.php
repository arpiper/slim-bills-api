<?php
namespace App\models;

class User {

    protected static $connection;

    public static function setConnection ($conn) {
        self::$connection = $conn->users;
        self::$connection->createIndex([ 'username' => 1 ], [ 'unique' => true ]);
    }

    public static function getUser ($username) {
        return self::$connection->findOne([
            'username' => $username,
        ]);
    }
    
    public static function createUser ($user) {
        $result = self::$connection->insertOne([
            'username' => $user['username'],
            'email' => $user['email'],
            'password' => password_hash($user['password'], PASSWORD_DEFAULT),
        ]);

        return $result;
    }

}
