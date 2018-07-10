<?php
namespace App\middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CsrfMiddleware extends Middleware {

    public function __invoke(Request $request, Response $response, $next) {
        if ($request->isPost() || $request->isPut()) {
            $token_key = $this->container->csrf->getTokenValueKey();
            $token = $request->getAttribute($token_key);
        }
        $csrf = [
            $this->container->csrf->getTokenNameKey() => $this->container->csrf->getTokenName(),
            $this->container->csrf->getTokenValueKey() => $this->container->csrf->getTokenValue(),
        ];
        $response = $next($request, $response);
        return $response;
    }
}
