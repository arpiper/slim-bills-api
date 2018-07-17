<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use App\models\Bill;

class BillController extends Controller {
    
    public function createBill (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $insertResult = Bill::createBill($data);
        $res = $res->withJson([
            'message' => 'bill inserted',
            'data' => [
                'billid' => $insertResult->getInsertedId(),
            ],
        ]);
        return $res;
    }

    public function readBill (Request $req, Response $res, array $args) {
        $bill = Bill::getBill($args['billid']);
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
