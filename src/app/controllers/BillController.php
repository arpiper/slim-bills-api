<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class BillController extends Controller {
    
    public function createBill (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $db = $this->container['mdb'];
        $insertResult = $db->bills->insertOne([
            'due_date' => filter_var($data['due_date']),
            'amount' => filter_var($data['amount'], FILTER_VALIDATE_FLOAT),
            'paid_to' => filter_var($data['paid_to']),
            'split_by_ids' => filter_var($data['split_by']),
            'split_count' => filter_var($data['split_count']),
            'split_amount' => filter_var($data['split_amount'], FILTER_VALIDATE_FLOAT),
            'paid_partial_ids' => filter_var($data['paid_partial_ids']),
            'paid_full' => filter_var($data['paid_full'], FILTER_VALIDATE_BOOLEAN),
            'paid_date' => filter_var($data['paid_date']),
            'notes' => filter_var($data['notes'], FILTER_SANITIZE_STRING),
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
            '_id' => new \MongoDB\BSON\ObjectId($args['billid']),
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
            ['_id' => new \MongoDB\BSON\ObjectId($args['billid'])],
            ['$set' => $data]
        );
    }

    public function deleteBill (Request $req, Response $res, array $args) {
        $db = $this->container['mdb'];
        $billid = new \MongoDB\BSON\ObjectId($args['billid']);
        $result = $bill->bills->deleteOne(['_id' => $billid]);
        
        $res = $res->withJson([
            'message' => 'Bill deleted',
            'data' => [
                'count' => $result->getDeletedCount(),
                'id' => $billid,
            ],
        ]);
        return $res;
    }
}
