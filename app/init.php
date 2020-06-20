<?php

use Phalcon\Mvc\Micro;

$config = include_once(__DIR__ . "/config.php");

include_once(__DIR__ . "/loader.php");

$di = include_once(__DIR__ . "/di.php");

// Create and bind the DI to the application
$app = new Micro();
$app->setDI($di);
$router = include_once(__DIR__ . "/router.php");

$request = new Phalcon\Http\Request();
$fixed_uri = str_replace('/robot-api-teste2/public', '', $request->getURI());
$app->handle($fixed_uri);
