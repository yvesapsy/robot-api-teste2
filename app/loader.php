<?php

$loader = new \Phalcon\Loader();
$loader->registerDirs(
    [
        __DIR__ . '/controllers/',
        __DIR__ . '/models/',
    ]
);
include(__DIR__ . '/controllers/RobotsController.php');
$loader->register();
