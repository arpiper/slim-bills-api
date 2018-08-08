<?php
namespace App\middleware;

use App\middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \Slim\Csrf\Guard;

class CsrfResponseMiddleware extends Middleware {

    public function __invoke(Request $req, Response $res, callable $next) {
        
        if (in_array($req->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $header = $req->getHeader('CSRF-Token');
            $token = json_decode($header);
            $name = isset($token['csrf_name']) ? $token['csrf_name'] : false;
            $value = isset($token['csrf_value']) ? $token['csrf_value'] : false;
            if (!$name || !$value || !$this->container->csrf->validateToken($name, $value)) {
                $req = $this->container->csrf->generateNewToken($req);
                $failureCallable = $this->getFailureCallable();
                return $failureCallable($req, $res, $next);
            }
        }
        // generate new token 
        $req = $this->container->csrf->generateNewToken($req);

        // build the header token
        $nameKey = $this->container->csrf->getTokenNameKey();
        $valueKey = $this->container->csrf->getTokenValueKey();
        $name = $req->getAttribute($nameKey);
        $value = $req->getAttribute($valueKey);
        $jsonToken = json_encode([
            $nameKey => $name,
            $valueKey => $value,
        ]);
        
        // update the response with the token header
        $res = $res->withAddedHeader('X-CSRF-Token', $jsonToken);
        $res = $res->withAddedHeader('Set-Cookie', "CSRF-Token=$jsonToken");
        return $next($req, $res);
    }
}
