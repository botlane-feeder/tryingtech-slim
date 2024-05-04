<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(false, true, true);

/** Tests sur les middlewares
 * la définition des middlewares s'ajoute dans un pile : la première function Middleware sera executées en dernier
 */

$beforeMV = function (Request $request, RequestHandler $handler) use ($app) {
  error_log("1");
  $response = $handler->handle($request);
  $existingContent = (string) $response->getBody();
  
  $response = $app->getResponseFactory()->createResponse();
  $response->getBody()->write('BEFORE ' . $existingContent);
  error_log($existingContent);
  
  return $response;
};

$afterMV = function (Request $request, RequestHandler $handler) {
  error_log("3");
  $response = $handler->handle($request);
  $response->getBody()->write(' AFTER');
  return $response;
};

// $app->add(function (Request $request, RequestHandler $handler) use ($app) {
//   error_log("2");
//   $response = $handler->handle($request);
//   $existingContent = (string) $response->getBody();

//   $response = $app->getResponseFactory()->createResponse();
//   $response->getBody()->write('\-' . $existingContent);

//   return $response;
// });

// $app->add(function (Request $request, RequestHandler $handler) use ($app) {
//   error_log("4");
//   $response = $handler->handle($request);
//   $response->getBody()->write('-/');
//   return $response;
// });

/** Routes de l'application
 * `/` : renvoie Hello World
 * `/about` : renvoie des données de l'application comme son nom et sa version
 */

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
})->add($beforeMV)->add($afterMV);

$app->get('/about', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode(["name"=>"test-slim", "version"=>"0.0.0"]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->group('/utils', function (RouteCollectorProxy $group) {
  $group->get('/', function (Request $request, Response $response) {
    $response->getBody()->write( "Choose your route utils/date or utils/time");
    return $response;
});
  $group->get('/date', function (Request $request, Response $response) {
      $response->getBody()->write(date('Y-m-d H:i:s'));
      return $response;
  });
  
  $group->get('/time', function (Request $request, Response $response) {
      $response->getBody()->write((string)time());
      return $response;
  });
})->add(function (Request $request, RequestHandler $handler) use ($app) {
  $response = $handler->handle($request);
  $dateOrTime = (string) $response->getBody();

  $response = $app->getResponseFactory()->createResponse();
  $response->getBody()->write('It is now ' . $dateOrTime . '. Enjoy!');

  return $response;
});

$app->run();
/* Exploration de la documentation :
- Request
- Response
- RequestHandler : pour comprendre et appréhender les middlewares

- Groupe de routes (Possibilité d'ajouter un middleware à un groupe de routes)
  - 
*/
