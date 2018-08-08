<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
/*
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};
*/

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// MongoDB database
$container['mdb'] = function ($c) {
    $db = $c['settings']['db'];
    $conn_str = "mongodb://$db[user]:$db[pass]@$db[host]:$db[port]/$db[authdb]";
    $mdb = App\models\MDB::getMDB($conn_str);#new \MongoDB\Client($conn_str);
    #$mdb = new \MongoDB\Client($conn_str);
    return $mdb;
};
App\models\Bill::setConnection($container['mdb']);
App\models\Person::setConnection($container['mdb']);
App\models\Utility::setConnection($container['mdb']);

// Authentication class
$container['auth'] = function($c) {
    return new App\auth\Auth;
};

# csrf guard
$container['csrf'] = function ($c) {
    $guard = new App\middleware\CsrfHeaderMiddleware;
    $guard->setPersistentTokenMode(true);
    return $guard;
};
