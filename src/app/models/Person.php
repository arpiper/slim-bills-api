<?php
namespace App\models;

class Person {

    protected static $connection;
    protected static $fields = [
        'name',
        'payments_made',
    ];
    protected static $filters = [
        'name' => FILTER_SANITIZE_STRING,
        'payments_made' => [
            'filter' => FILTER_VALIDATE_FLOAT,
            'options' => [
                'default' => 0,
            ],
        ],
    ];

    public static function setConnection ($conn) {
        self::$connection = $conn->persons;
    }

    public static function getPerson ($personId) {
        $person = self::$connection->findOne([
            '_id' => new \MongoDB\BSON\ObjectID($personId)
        ]);
        return $person;
    }

    public static function getPersons () {
        $results = self::$connection->find();
        $persons = [];
        $count = 0;
        foreach ($results as $person) {
            $persons[(string)$person['_id']] = $person;
            $count++;
        }
        return ['persons' => $persons, 'count' => $count];
    }

    public static function createPerson ($data) {
        $filtered = filter_var_array($data, self::$filters);
        if (!$filtered['payments_made']) {
            $filtered['payments_made'] = 0;
        }
        $result = self::$connection->insertOne($filtered);
        return $result->getInsertedId();
    }

    public static function updatePerson ($personId, $data) {
        $filtered = filter_var_array($data, self::$filters);
        $updatedPerson = self::$connection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($personId)],
            ['$set' => $filtered]
        );
        return $updatedPerson->getModifiedCount();
    }

}
