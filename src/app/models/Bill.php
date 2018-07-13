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

    public static function setConnection ($connection) {
        self::$connection = $connection->bills;
    }

    public static function getBill ($id) {
        self::setConnection(MDB::getMDB());
        $bill = self::$connection->findOne([
            "_id" => new \MongoDB\BSON\ObjectId($id)
        ]);
        return $bill;
    }

    public static function createBill ($data) {
        $bill = [];
        foreach ($date as $key => $value) {
            if (!in_array($key, self::$fields)) {
                return 'invalid bill';
            }
            # float values
            if (in_array($key, ['amount', 'split_amount'])) {
                $bill[$key] = filter_var($value, FILTER_VALIDATE_FLOAT);
            # integer values
            } elseif (in_array($key, ['paid_partial_ids', 'split_by_ids', 'split_count']) {
                if ($key === 'split_count') {
                    $bill[$key] = filter_var($value, FILTER_VALIDATE_INT);
                } else {
                    $bill[$key] = filter_var($value, FILTER_CALLBACK, array('options' => self::filterArray));
                }
            # string values
            } elseif (in_array($key, ['notes', 'due_date', 'paid_date'])) {
                $bill[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            # object values
            } elseif (in_array($key, ['paid_to', 'split_by', 'paid_partial'])) {
                $bill[$key] = filter_var($value, FILTER_CALLBACK, array('options' => selff::filterObject));
            # boolean values
            } elseif (in_array($key, ['paid_full'])) {
                $bill[$key] => filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }
        $result = self::$connection->insertOne($bill);
        return $result;
    }

    public static function filterArray ($values) {
        foreach ($values as $v) {
            if (!filter_var($v, FILTER_VALIDATE_INT)) {
                return false;
            }
        }
        return $values;
    }

    public static function filterObject ($value) {
        return true;
    }
}
