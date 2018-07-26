<?php
#namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\controllers\BillController as BillC;
use App\controllers\PersonController as PersonC;
use App\controllers\UtilityController as UtilC;
use App\middleware\CsrfMiddleware;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->group('/bills', function () {
    $this->get('', BillC::class . ':readBills')->setName('bills');
    $this->post('', BillC::class . ':createBill')->setName('bills');
    $this->get('/{id}', BillC::class . ':readBill')->setName('bill');
    $this->map(['post', 'put'],'/{id}', BillC::class . ':updateBill')->setName('updateBill');
    $this->delete('/{billid}', BillC::class . ':deleteBill')->setName('deleteBill');
});

$app->group('/persons', function () {
    $this->get('', PersonC::class . ':readPersons')->setName('persons');
    $this->post('', PersonC::class . ':createPerson')->setName('persons');
    $this->get('/{id}', PersonC::class . ':readPerson')->setName('person');
    $this->map(['post', 'put'], '/{id}', PersonC::class . ':updatePerson')->setName('updatePerson');
    $this->delete('/{personid}', PersonC::class . ':deletePerson')->setName('deletePerson');
});

$app->group('/utilities', function () {
    $this->get('', UtilC::class . ':readUtilities')->setName('utilities');
    $this->post('', UtilC::class . ':createUtility')->setName('utilities');
    $this->get('/{id}', UtilC::class . ':readUtility')->setName('utility');
    $this->map(['post', 'put'], '/{id}', UtilC::class . ':updateUtility')->setName('updateUtility');
    $this->delete('/{utilityid}', UtilC::class . ':deletePerson')->setName('deleteUtility');
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $routes = [];
    foreach ($this->router->getRoutes() as $route) {
        //array_push($routes, $route->getPattern());
        $routes[$route->getName()] = $route->getPattern();
    }
    return $response->withJson(['message' => 'document root', 'data' => $routes]);
});


#$app->add(new CsrfMiddleware($container));

#$app->add($container->csrf);

// trailing / redirection middleware
$app->add(function (Request $req, Response $res, callable $next) {
    $uri = $req->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash 
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));
        if ($req->getMethod() == 'GET') {
            return $res->withRedirect((string)$uri, 301);
        } else {
            return $next($req->withUri($uri), $res);
        }
    }
    return $next($req, $res);
});

// CORS middleware
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-with, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS'); 
});
