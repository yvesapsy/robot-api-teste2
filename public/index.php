<?php

use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;

$loader = new Loader();

$loader->registerNamespaces(['Store\Toys' => __DIR__ . '/models/']);

$loader->register();

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

// Create and bind the DI to the application
$app = new Micro($di);

// Retrieves all robots
$app->get(
    '/api/robots',
    function () use ($app) {
        $phql = 'SELECT
                    *
                FROM 
                    Store\Toys\Robots
                ORDER BY
                    name';

        $robots = $app->modelsManager->executeQuery($phql);

        $data = [];

        foreach ($robots as $robot) {
            $data[] = [
                'id'   => $robot->id,
                'name' => $robot->name
            ];
        }

        $response = new Response();
        $response->setJsonContent($data);

        return $response;
    }
);

// Searches for robots with $name in their name
$app->get(
    '/api/robots/search/{name}',
    function ($name) use ($app) {
        $phql = 'SELECT
                    *
                FROM
                    Store\Toys\Robots
                WHERE
                    name LIKE :name:
                ORDER BY
                    name';

        $robots = $app->modelsManager->executeQuery(
            $phql,
            [
                'name' => '%' . $name . '%'
            ]
        );

        $data = [];

        foreach ($robots as $robot) {
            $data[] = [
                'id'   => $robot->id,
                'name' => $robot->name,
            ];
        }

        $response = new Response();
        $response->setJsonContent($data);

        return $response;
    }
);

// Retrieves robots based on primary key
$app->get(
    '/api/robots/{id:[0-9]+}',
    function ($id) use ($app) {
        $phql = 'SELECT
                    *
                FROM
                    Store\Toys\Robots
                WHERE
                    id = :id:';

        $robot = $app->modelsManager->executeQuery(
            $phql,
            [
                'id' => $id
            ]
        )->getFirst();

        $response = new Response();

        if ($robot === null) {
            $response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {
            $response->setJsonContent(
                [
                    'status' => 'FOUND',
                    'data'   => [
                        'id'   => $robot->id,
                        'name' => $robot->name,
                        'type' => $robot->type,
                        'year' => $robot->year
                    ]
                ]
            );
        }

        return $response;
    }
);

// Adds a new robot
$app->post(
    '/api/robots',
    function () use ($app) {
        $robot = $app->request->getJsonRawBody();

        $phql = 'INSERT INTO Store\Toys\Robots (
                    name,
                    type,
                    year
                ) VALUES (
                    :name:,
                    :type:,
                    :year:
                )';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                'name' => $robot->name,
                'type' => $robot->type,
                'year' => $robot->year
            ]
        );

        $response = new Response();

        // Check if the insertion was successful
        if ($status->success() === true) {
            // Change the HTTP status
            $response->setStatusCode(201, 'Created');

            $robot->id = $status->getModel()->id;

            $response->setJsonContent(
                [
                    'status' => 'OK',
                    'data'   => $robot
                ]
            );
        } else {
            // Change the HTTP status
            $response->setStatusCode(409, 'Conflict');

            // Send errors to the client
            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors
                ]
            );
        }

        return $response;
    }
);

// Updates robots based on primary key
$app->put(
    '/api/robots/{id:[0-9]+}',
    function ($id) use ($app) {
        $phql = 'SELECT
                    *
                FROM
                    Store\Toys\Robots
                WHERE
                    id = :id:';

        $robot = $app->modelsManager->executeQuery(
            $phql,
            [
                'id' => $id
            ]
        )->getFirst();

        $response = new Response();

        if ($robot === null) {
            $response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {
            $robot = $app->request->getJsonRawBody();

            $phql = 'UPDATE
                        Store\Toys\Robots
                    SET
                        name = :name:,
                        type = :type:,
                        year = :year:
                    WHERE
                        id = :id:';

            $status = $app->modelsManager->executeQuery(
                $phql,
                [
                    'id'   => $id,
                    'name' => $robot->name,
                    'type' => $robot->type,
                    'year' => $robot->year
                ]
            );

            // Check if the insertion was successful
            if ($status->success() === true) {
                $response->setJsonContent(
                    [
                        'status' => 'OK'
                    ]
                );
            } else {
                // Change the HTTP status
                $response->setStatusCode(409, 'Conflict');

                $errors = [];

                foreach ($status->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                $response->setJsonContent(
                    [
                        'status'   => 'ERROR',
                        'messages' => $errors
                    ]
                );
            }
        }

        return $response;
    }
);

// Deletes robots based on primary key
$app->delete(
    '/api/robots/{id:[0-9]+}',
    function ($id) use ($app) {
        $phql = 'SELECT
                    *
                FROM
                    Store\Toys\Robots
                WHERE
                    id = :id:';

        $robot = $app->modelsManager->executeQuery(
            $phql,
            [
                'id' => $id
            ]
        )->getFirst();

        $response = new Response();

        if ($robot === null) {
            $response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {
            $phql = 'DELETE FROM
                        Store\Toys\Robots
                    WHERE
                        id = :id:';

            $status = $app->modelsManager->executeQuery(
                $phql,
                [
                    'id' => $id
                ]
            );

            if ($status->success() === true) {
                $response->setJsonContent(
                    [
                        'status' => 'OK'
                    ]
                );
            } else {
                // Change the HTTP status
                $response->setStatusCode(409, 'Conflict');

                $errors = [];

                foreach ($status->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                $response->setJsonContent(
                    [
                        'status'   => 'ERROR',
                        'messages' => $errors
                    ]
                );
            }
        }

        return $response;
    }
);

$request = new Phalcon\Http\Request();
$fixed_uri = str_replace('/robot-api-teste1/public', '', $request->getURI());
$app->handle($fixed_uri);
