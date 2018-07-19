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
    $this->get('', BillController::class . ':readBills')->setName('bills');

    $this->post('', BillController::class . ':createBill')->setName('bills');

    $this->get('/{billid}', BillController::class . ':readBill')->setName('bill');

    $this->map(['post', 'put'],'/{billid}', BillController::class . ':updateBill')->setName('updateBill');

    $this->delete('/{billid}', BillController::class . ':deleteBill')->setName('deleteBill');
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $routes = [];
    foreach ($this->router->getRoutes() as $route) {
        //array_push($routes, $route->getPattern());
        $routes[$route->getName()] = $route->getPattern();
    }
    var_dump($this->mdb);
    return $response->withJson(['message' => 'document root', 'data' => $routes]);
});


#$app->add(new CsrfMiddleware($container));

#$app->add($container->csrf);

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://example.com')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-with, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS'); 
});
