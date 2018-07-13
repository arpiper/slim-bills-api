<?php
namespace App\models;

class MDB {
    
    protected static $connection;

    public static function getMDB($conn_str = '') {
        if (!self::$connection) self::$connection = new \MongoDB\Client($conn_str);
        return self::$connection->BillTracker;
    }
}
