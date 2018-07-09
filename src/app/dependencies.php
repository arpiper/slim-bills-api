<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

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
    $mdbClient = new \MongoDB\Client($conn_str);
    return $mdbClient->BillTracker;
};