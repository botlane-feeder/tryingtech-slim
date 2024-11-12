<?php

use App\App;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();

$app->buildRoutes()
->handleOPTIONSforCORS()
->getApp()
->run();
