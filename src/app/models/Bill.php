<?php
namespace App\models;

use App\models\Person;

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
            'filter' => FILTER_SANITIZE_STRING, # FILTER_VALIDATE_INT
            'flags' => FILTER_FORCE_ARRAY,
        ],
        'paid_to' => FILTER_SANITIZE_STRING, # FILTER_VALIDATE_INT,
    ];

    public static function setConnection ($connection) {
        self::$connection = $connection->bills;
    }

    public static function getBill ($id) {
        $bill = self::$connection->findOne([
            "_id" => new \MongoDB\BSON\ObjectId($id)
        ]);
        return $bill;
    }

    public static function getBills () {
        $result = self::$connection->find();
        $bills = [];
        $count = 0;
        foreach ($result as $bill) {
            $bills[(string)$bill['_id']] = $bill;
            $count++;
        }
        return ['bills' => $bills, 'count'=> $count];
    }

    public static function createBill ($data) {
        $filtered = filter_var_array($data, self::$filters);
        $persons = [];
        foreach ($filtered['split_by_ids'] as $personId) {
            $persons[$personId] = Person::getPerson($person_id);
        }
        $filtered['paid_to'] = Utility::getUtility($filtered['paid_to']);
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
        #var_dump(self::$connection);
        $result = self::$connection->insertOne($bill);
        return $result->getInsertedId();
    }

    public static function updateBill ($billid, $data) {
        $filtered = filter_var_array($data, self::$filters);
        $updatedBill = self::$connection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($billid)],
            ['$set' => $filtered]
        );
        return $updatedBill->getModifiedCount();
    }
}
