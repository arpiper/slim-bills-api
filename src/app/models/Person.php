<?php
namespace App\models;

class Person {

    protected static $connection;
    protected static $fields = [
        'name',
        'payments_made',
    ];
    protected static $filters = [
        'id' => FILTER_SANITIZE_STRING,
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
            $persons[] = $person;
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
        $filtered['id'] = (string)$result->getInsertedId();
        self::updatePerson($filtered['id'], $filtered);
        return $filtered['id'];
    }

    public static function updatePerson ($personId, $data) {
        $filtered = filter_var_array($data, self::$filters);
        if (count($filtered) > 0) {
            $updates = [];
            if (isset($filtered['payments_made'])) {
                $updates['$inc'] = ['payments_made' => $filtered['payments_made']];
            }
            if (isset($filtered['name'])) {
                $updates['$set'] = ['name' => $filtered['name']];
            }
            $updatedPerson = self::$connection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($personId)],
                $updates
            );
            return $updatedPerson->getModifiedCount();
        }
        return 0;
    }

}
