<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Ajoutez les middleware et les routes
// print( (__DIR__ . '/../src/middleware/jwt_middleware.php') );
// print( json_encode(realpath(__DIR__ . '/../src/middleware/jwt_middleware.php') ));

require __DIR__ . '/../src/middleware/jwt-middleware.php';
require __DIR__ . '/../src/routes.php';

$app->run();
