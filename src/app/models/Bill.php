<?php
namespace App\models;

class Bill {
    
    protected static $connection;
    protected static $fields = [
        'amount',
        'due_date',
        'notes',
        'paid_date',
        'paid_full',
        'paid_partial',
        'paid_partial_ids',
        'paid_to',
        'split_amount',
        'split_by',
        'split_by_ids',
        'split_count',
    ];
    protected static $filters = [
        'amount' => FILTER_VALIDATE_FLOAT,
        'due_date' => FILTER_SANITIZE_STRING,
        'notes' => FILTER_SANITIZE_STRING,
        'split_amount' => FILTER_VALIDATE_FLOAT,
        'split_count' => FILTER_VALIDATE_INT,
        'split_by_ids' => [
            'filter' => FILTER_VALIDATE_INT,
            'flags' => FILTER_FORCE_ARRAY,
        ],
        'paid_to' => FILTER_VALIDATE_INT,
    ];

    public static function setConnection ($connection) {
        self::$connection = $connection->bills;
    }

    public static function getBill ($id) {
        self::setConnection(MDB::getMDB());
        $bill = self::$connection->findOne([
            "_id" => new \MongoDB\BSON\ObjectId($id)
        ]);
        var_dump(self::$connection);
        return $bill;
    }

    public static function getBills () {
        $bills = self::$connection->find();
        return $bills;
    }

    public static function createBill ($data) {
        self::setConnection(MDB::getMDB());
        $filtered = filter_var_array($data, self::$filters);
        $persons = [];
        foreach ($filtered['split_by_ids'] as $person_id) {
            //$persons[$person_id] = Person::getPerson($person_id);
            // placeholder values
            $persons[$person_id] = 'App\models\Person';
        }

        $bill = array_merge(
            $filtered,
            [
                'paid_full' => false,
                'paid_partial_ids' => [],
                'paid_partials' => [],
                'paid_date' => '',
                'split_by' => $persons,
            ]
        );
        var_dump(self::$connection);
        $result = self::$connection->insertOne($bill);
        return $result;
    }
}
