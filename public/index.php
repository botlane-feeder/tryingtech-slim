<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// $app->addErrorMiddleware(false, true, true);

/** Emplacement pour les middlewares
 * la définition des middlewares s'ajoute dans un pile : la première function Middleware sera executées en dernier
 */

/** Routes de l'application
 * `/` : renvoie Hello World
 * `/about` : renvoie des données de l'application comme son nom et sa version
 */

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/about', function (Request $request, Response $response, $args) {
    $response->getBody()
    ->write(
      json_encode(["name"=>"test-slim", "version"=>"0.0.0"])
    );

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/lists', function (Request $request, Response $response, $args) {
  $manager = new MongoDB\Driver\Manager('mongodb://mongodb:27017');
  $listsArray = $manager->executeQuery("todolist.lists", new \MongoDB\Driver\Query([]))->toArray();
  
  $client = new MongoDB\Client('mongodb://mongodb:27017');
  $listsCollection = new MongoDB\Collection($client->getManager(), "todolist", "lists");
  $allDocuments = $listsCollection->find([])->toArray();

  $response->getBody()
  ->write(json_encode(array_merge($listsArray, $allDocuments)));

  return $response->withHeader('Content-Type', 'application/json');
});

$app->run();

/* Exploration de la documentation :
- Request
- Response
- RequestHandler : pour comprendre et appréhender les middlewares

- Groupe de routes (Possibilité d'ajouter un middleware à un groupe de routes)
  - 
*/
