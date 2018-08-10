<?php
namespace App\models;

class User {

    protected static $connection;
    protected static $filters = [
        'username' => FILTER_SANITIZE_STRING,
        'email' =>  FILTER_VALIDATE_EMAIL,
    ];

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
        $username = filter_var($user['username'], self::$filters['username']);
        $email = filter_var($user['email'], self::$filters['email']);
        $password = password_hash($user['password'], PASSWORD_DEFAULT);
        $result = self::$connection->insertOne([
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]);
        return $result;
    }
}
