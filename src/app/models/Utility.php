<?php
namespace App\models;

class Utility {

    protected static $connection;
    protected static $fields = [
        'name',
        'payments',
    ];
    protected static $filters = [
        'id' => FILTER_SANITIZE_STRING,
        'name' => FILTER_SANITIZE_STRING,
        'payments' => [
            'filter' => FILTER_VALIDATE_FLOAT,
            'options' => [
                'default' => 0,
            ],
        ],
    ];

    public static function setConnection ($conn) {
        self::$connection = $conn->utilities;
    }

    public static function getUtility ($utilityId) {
        $utility = self::$connection->findOne([
            '_id' => new \MongoDB\BSON\ObjectID($utilityId)
        ]);
        return $utility;
    }

    public static function getUtilities () {
        $results = self::$connection->find();
        $utilities = [];
        $count = 0;
        foreach ($results as $util) {
            $utilities[] = $util;
            $count++;
        }
        return ['utilities' => $utilities, 'count' => $count];
    }

    public static function createUtility ($data) {
        $filtered = filter_var_array($data, self::$filters);
        if (!$filtered['payments']) {
            $filtered['payments'] = 0;
        }
        $result = self::$connection->insertOne($filtered);
        $filtered['id'] = (string)$result->getInsertedId();
        self::updateUtility($filtered['id'], $filtered);
        return $result->getInsertedId();
    }

    public static function updateUtility ($utilityId, $data) {
        $filtered = filter_var_array($data, self::$filters);
        $updatedUtil = self::$connection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($utilityId)],
            ['$set' => $filtered]
        );
        return $updatedUtil->getModifiedCOunt();
    }
}
