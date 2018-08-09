<?php
#namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\controllers\BillController as BillC;
use App\controllers\PersonController as PersonC;
use App\controllers\UtilityController as UtilC;
use App\controllers\UserController as UserC;
use App\controllers\AuthController as AuthC;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// main api routes to /bills, /persons, /utilities
$app->group('/api', function () {
    $this->group('/bills', function () {
        $this->get('', BillC::class . ':readBills')->setName('bills');
        $this->post('', BillC::class . ':createBill')->setName('bills');
        $this->get('/{id}', BillC::class . ':readBill')->setName('bill');
        $this->put('/{id}', BillC::class . ':updateBill')->setName('updateBill');
        $this->delete('/{billid}', BillC::class . ':deleteBill')->setName('deleteBill');
    });

    $this->group('/persons', function () {
        $this->get('', PersonC::class . ':readPersons')->setName('persons');
        $this->post('', PersonC::class . ':createPerson')->setName('persons');
        $this->get('/{id}', PersonC::class . ':readPerson')->setName('person');
        $this->put('/{id}', PersonC::class . ':updatePerson')->setName('updatePerson');
        $this->delete('/{personid}', PersonC::class . ':deletePerson')->setName('deletePerson');
    });

    $this->group('/utilities', function () {
        $this->get('', UtilC::class . ':readUtilities')->setName('utilities');
        $this->post('', UtilC::class . ':createUtility')->setName('utilities');
        $this->get('/{id}', UtilC::class . ':readUtility')->setName('utility');
        $this->put('/{id}', UtilC::class . ':updateUtility')->setName('updateUtility');
        $this->delete('/{utilityid}', UtilC::class . ':deletePerson')->setName('deleteUtility');
    });
})->add($container->csrf);

// api routes for user authentication/creation
$app->group('/api', function () {

    $this->post('/users', UserC::class . ':createUser')->setName('users');
    $this->put('/users/{id}', UserC::class . ':updateUser')->setName('updateUser');

    $this->post('/login', AuthC::class . ':login')->setName('login');
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $routes = [];
    foreach ($this->router->getRoutes() as $route) {
        $routes[$route->getName()] = $route->getPattern();
    }
    return $response->withJson(['message' => 'document root', 'data' => $routes]);
});

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
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader(
                'Access-Control-Allow-Headers', 
                'X-Requested-with, Content-Type, Accept, Origin, Authorization, X-CSRF-Token, CSRF-Token')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS'); 
});

// JWT authorization middleware.
$app->add(new \Tuupola\Middleware\JwtAuthentication([
    'path' => ['/api'],
    'ignore' => ['/api/users', '/api/login'],
    'secret' => 'supersecretkeyyoushouldntcommit', // set as env variable for production.
]));
