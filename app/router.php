<?php

$robotsCollection = new \Phalcon\Mvc\Micro\Collection();
$robotsCollection->setHandler('Store\Controllers\RobotsController', true);
$robotsCollection->setPrefix('/api');
$robotsCollection->get   ('/robots',               'list'  );
$robotsCollection->get   ('/robots/search/{name}', 'search');
$robotsCollection->get   ('/robots/{id:[0-9]+}',   'info'  );
$robotsCollection->post  ('/robots',               'create');
$robotsCollection->put   ('/robots/{id:[0-9]+}',   'update');
$robotsCollection->delete('/robots/{id:[0-9]+}',   'delete');
$app->mount($robotsCollection);

// not found URLs
$app->notFound(
  function () use ($app) {
      echo "not found";
  }
);
