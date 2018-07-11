<?php
#namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\controllers\BillController;
use App\middleware\CsrfMiddleware;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->group('/bills', function () {
    $this->get('', BillController::class . ':readBills');

    $this->post('', BillController::class . ':createBill');

    $this->get('/{billid}', BillController::class . ':readBill');

    $this->map(['post', 'put'],'/{billid}', BillController::class . ':updateBill');

    $this->delete('/{billid}', BillController::class . ':deleteBill');
});

$app->get('/', function (Request $request, Response $response, array $args) {
    return $response->withJson(['message' => 'document root', 'data' => []]);
});


$app->add(new CsrfMiddleware($container));

$app->add($container->csrf);

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://example.com')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-with, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS'); 
});
