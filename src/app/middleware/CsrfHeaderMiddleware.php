<?php
namespace App\middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \Slim\Csrf\Guard;

class CsrfHeaderMiddleware extends Guard {

    public function __invoke(Request $request, Response $response, callable $next) {
        $this->validateStorage();
        // check header in changing http methods
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) { 
            $failedCsrf = true;
            if ($request->hasHeader('CSRF-Token')) {
                $header = $request->getHeader('CSRF-Token');
                // getHeader returns an array. the token should be in 
                // a assoc. array format with the following keys.
                $token = json_decode($header[0], true); 
                $name = isset($token['csrf_name']) ? $token['csrf_name'] : false;
                $value = isset($token['csrf_value']) ? $token['csrf_value'] : false;
                if ($name && $value && $this->validateToken($name, $value)) {
                    $failedCsrf = false;
                }
            } 
            if ($failedCsrf) {
                // generate new token, validateToken will remove the current one.
                $request = $this->generateNewToken($request);
                $failureCallable = $this->getFailureCallable();
                return $failureCallable($request, $response, $next);
            }
        }
        
        if (!$this->persistentTokenMode || !$this->loadLastKeyPair()) {
            $request = $this->generateNewToken($request);
        } elseif ($this->persistentTokenMode) {
            $pair = $this->loadLastKeyPair() ? $this->keyPair : $this->generateToken();
            $request = $this->attachRequestAttributes($request, $pair);
        }

        // build the CSRF-Token header
        $nameKey = $this->getTokenNameKey();
        $valueKey = $this->getTokenValueKey();
        $jsonToken = json_encode([
            $nameKey => $request->getAttribute($nameKey),
            $valueKey => $request->getAttribute($valueKey),
        ]);

        // update the response header
        $response = $response
                        ->withAddedHeader('X-CSRF-Token', $jsonToken)
                        ->withAddedHeader('Set-Cookie', "CSRF-Token=$jsonToken");

        // enforce the storage limit.
        $this->enforceStorageLimit();

        return $next($request, $response);
    }
}
