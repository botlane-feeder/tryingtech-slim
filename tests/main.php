<?php

use App\App;

use Slim\Psr7\Factory\ServerRequestFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
$app->buildRoutes();

$request = (new ServerRequestFactory())->createServerRequest('GET', '/lists');
$response = $app->handle($request);

print(json_encode(json_decode($response->getBody()->__toString(), true)));

print("\n\n");
print("VOILA\n");