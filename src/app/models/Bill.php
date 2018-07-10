<?php
namespace App\models;

class Bill {
    protected $id;
    protected $due_date;
    protected $amount;
    protected $paid_to;
    protected $split_by_ids;
    protected $split_count;
    protected $split_amount;
    protected $paid_partial_ids;
    protected $paid_full;
    protected $paid_date;
    protected $notes;

    protected $db_connection;

    public function __construct (array $data) {
        $this->db_connection = $data['db'];

    }

    public function getBill ($id, $user) {
        $bill = $this->db_connection->find([
            '_id' => $id,
            'user' => $user,
        ]);
        return $bill;
    }
}
