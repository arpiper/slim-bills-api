<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class BillController {
    
    protected $container;

    public function __construct (Container $container) {
        $this->container = $container;
    }

    public function createBill (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $db = $this->container['mdb'];
        $insertResult = $db->bills->insertOne([
            'due_date' => $data['due_date'],
            'amount' => $data['amount'],
            'paid_to' => $data['paid_to'],
            'split_by_ids' => $data['split_by'],
            'split_count' => $data['split_count'],
            'split_amount' => $data['split_amount'],
            'paid_partial_ids' => $data['paid_partial_ids'],
            'paid_full' => $data['paid_full'],
            'paid_date' => $data['paid_date'],
            'notes' => $data['notes'],
        ]);

        $res = $res->withJson([
            'message' => 'bill inserted',
            'data' => [
                'billid' => $insertResult->getInsertedId(),
            ],
        ]);
        return $res;
    }

    public function readBill (Request $req, Response $res, array $args) {
        $db = $this->container['mdb'];
        $bill = $db->bills->findOne([
            '_id' => new \MongoDB\BSON\ObjectId($args[billid]),
        ]);
        return $res->withJson($bill);
    }

    public function readBills (Request $req, Response $res, array $args) {
        $db = $this->container['mdb'];

        $list = $db->bills->find();
        foreach ($list as $l) {
            var_dump($l);
            $res->getBody()->write($l['_id'] . '<br />');
        }

        return $res;
    }

    public function updateBill (Request $req, Response $res, array $args) {
        $db = $this->container['mdb'];
        $data = $req->getParsedBody();
        $bill = $db->bills->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($args[billid])],
            ['$set' => $data],
        );
    }

    public function deleteBill (Request $req, Response $res, array $args) {
    }
}
