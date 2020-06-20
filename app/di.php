<?php

use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Di\FactoryDefault;

$di = new FactoryDefault();

// Set up the database service
$di->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'password',
                'dbname'   => 'robotics'
            ]
        );
    }
);

return $di;