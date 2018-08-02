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
        'id' => FILTER_SANITIZE_STRING,
        'amount' => FILTER_VALIDATE_FLOAT,
        'due_date' => FILTER_SANITIZE_STRING,
        'notes' => FILTER_SANITIZE_STRING,
        'split_amount' => FILTER_VALIDATE_FLOAT,
        'split_count' => FILTER_VALIDATE_INT,
        'split_by_ids' => [
            'filter' => FILTER_SANITIZE_STRING, # FILTER_VALIDATE_INT
            'flags' => FILTER_FORCE_ARRAY,
        ],
        'paid_full' => FILTER_VALIDATE_BOOLEAN,
        'paid_partial_ids' => [
            'filter' => FILTER_SANITIZE_STRING,
            'flags' => FILTER_FORCE_ARRAY,
        ],
        'paid_date' => FILTER_SANITIZE_STRING,
        // the following value(s) are Person or Utility objects.
        'paid_to' => FILTER_SANITIZE_STRING,
        'split_by' => FILTER_UNSAFE_RAW,
        'paid_partial' => FILTER_UNSAFE_RAW,
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
            $bills[] = $bill;
            $count++;
        }
        return ['bills' => $bills, 'count'=> $count];
    }

    public static function createBill ($data) {
        $filters = array_merge(
            self::$filters,
            ['paid_to' => FILTER_SANITIZE_STRING]
        );
        $filtered = filter_var_array($data, self::$filters);
        // set the split_by value with Person objects.
        $persons = [];
        foreach ($filtered['split_by_ids'] as $personId) {
            $persons[] = Person::getPerson($personId);
        }
        // set the paid_to value with a Utility object, 
        // value should initially come in as a string id.
        $filtered['paid_to'] = Utility::getUtility($filtered['paid_to']);
        $bill = array_merge(
            $filtered,
            [
                'paid_full' => false,
                'paid_partial_ids' => [],
                'paid_partial' => [],
                'paid_date' => '',
                'split_by' => $persons,
            ]
        );
        $bill['id'] = (string)self::$connection->insertOne($bill)->getInsertedId();
        // add 'standard' id field in addition to mongodb's '_id' object.
        Bill::updateBill($bill['id'], $bill);
        return $bill['id'];
    }

    public static function updateBill ($billid, $data) {
        // get the values that are Mongo BSON's to backup. 
        // filter_var will nullify them.
        // manually check the mongo BSON's.
        $bsons = [
            'paid_to' => self::checkUtilityBSON($data['paid_to']),
            'split_by' => self::checkPersonBSON($data['split_by']),
            'paid_partial' => self::checkPersonBSON($data['paid_partial']),
        ];
        $filtered = filter_var_array($data, self::$filters);
        // merge the separately checked arrays.
        $filtered = array_merge($filtered, $bsons);

        if (count($filtered) > 0) {
            $update = self::$connection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($billid)],
                ['$set' => $filtered]
            );
            return $update->getModifiedCount();
        }
        return 0;
    }

    public static function checkUtilityBSON ($value) {
        return Utility::getUtility($value['id']);
    }

    public static function checkPersonBSON ($values) {
        $arr = [];
        foreach ($values as $v) {
            $arr[] = Person::getPerson($v['id']);
        }
        return $arr;
    }
}
