<?php
namespace App\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use App\models\Bill;

class BillController extends Controller {
    
    public function createBill (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $billid = Bill::createBill($data);
        $res = $res->withJson([
            'message' => 'bill inserted',
            'data' => [
                'billid' => $billid,
            ],
        ]);
        return $res;
    }

    public function readBill (Request $req, Response $res, array $args) {
        $bill = Bill::getBill($args['id']);
        $res = $res->withJson([
            'message' => "retreived bill $args[id]",
            'data' => [
                'bill' => $bill,
            ],
        ]);
        return $res;
    }

    public function readBills (Request $req, Response $res, array $args) {
        $bills = Bill::getBills();
        $res = $res->withJson([
            'message' => "$bills[count] bills found",
            'data' => $bills,
        ]);
        return $res;
    }

    public function updateBill (Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $updated = Bill::updateBill($args['id'], $data);
        $message = 'Error occured updating bill';
        if ($udpated == 1) {
            $message = "$args[id] successfully updated";
        }
        $res = $res->withJson([
            'message' => $message,
            'data' => [
                'bill' => $args['id'],
                'updated' => $updated,
            ],
        ]);
        return $res;
    }

    public function deleteBill (Request $req, Response $res, array $args) {
        $res = $res->withJson([
            'message' => 'bill successfully deleted',
            'data' => [
                'truthiness' => 'placeholder function, nothing actually changed',
                'count' => 1,
            ],
        ]);
        return $res;
    }
}
