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

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/about', function (Request $request, Response $response, array $args) {
    $response->getBody()
    ->write(
      json_encode(["name"=>"test-slim", "version"=>"0.0.0"])
    );

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/lists', function (Request $request, Response $response, array $args) {
  $manager = new MongoDB\Driver\Manager('mongodb://mongodb:27017');
  $taskArray = json_decode(json_encode($manager->executeQuery("todolist.lists", new \MongoDB\Driver\Query([]))->toArray()), true);
  
  $client = new MongoDB\Client('mongodb://mongodb:27017');
  $listsCollection = new MongoDB\Collection($client->getManager(), "todolist", "lists");
  $taskArray = json_decode(json_encode( $listsCollection->find([])->toArray()), true);

  foreach ($taskArray as $key => $oneTask) {
    $oneTask["id"]=$oneTask["_id"]['$oid'];
    unset($oneTask["_id"]);
    $taskArray[$key] = $oneTask;
  }

  $response->getBody()
  ->write(json_encode($taskArray));

  return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/task', function(Request $request, Response $response, array $args) {
  //fetch("/task", { method:"POST", headers:{"Content-Type": "application/json"}, body:JSON.stringify({"title":"Faire le tuto Svelte", "description":"Suivre tout le tutoriel pour se former à Slim"})} ); 
  $success=0;
  //Récupération des données en body
  $postParameters = json_decode($request->getBody(), true);
  // Vérification des bonnes données avant écriture
  if( isset($postParameters["title"]) ){
    // Création de la donnée `task`
    $task=[
      "title"=>$postParameters["title"],
      "description"=>$postParameters["description"]??"",
      "date"=>time(),
      "done"=>false
    ];
    // Connexion à la BDD
    $manager = new MongoDB\Driver\Manager('mongodb://mongodb:27017');
    $listsCollection = new MongoDB\Collection($manager, "todolist", "lists");
    // Ajout de la donnée `task`
    $listsCollection->insertOne($task);
  }else{
    error_log("No title in parameters");
    $success=1;
  }

  $response->getBody()->write(json_encode(["sucess"=>$success]));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/task/{idTask}', function(Request $request, Response $response, array $args) {
  //fetch("/task/"+idTask, { method:"GET", headers:{"Content-Type": "application/json"}} ); 
  $success=0;
  if( $args["idTask"] !== null ){
    // Connexion à la BDD
    $manager = new MongoDB\Driver\Manager('mongodb://mongodb:27017');
    $listsCollection = new MongoDB\Collection($manager, "todolist", "lists");
    $oneTask = $listsCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($args["idTask"])]);
  }

  $response->getBody()->write(json_encode($oneTask));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/task/{idTask}', function(Request $request, Response $response, array $args) {
  //fetch("/task/"+idTask, { method:"PUT", headers:{"Content-Type": "application/json"}, body:JSON.stringify({"description":"Nouvelle description"})} ); 
  $success=0;
  //Récupération des données en body
  $putParameters = json_decode($request->getBody(), true);
  $updatesTask=[];
  if( isset($putParameters["title"]) ) $updatesTask["title"] = $putParameters["title"];
  if( isset($putParameters["description"]) ) $updatesTask["description"] = $putParameters["description"];

  if( $updatesTask != [] && $args["idTask"] !== null){
    // Connexion à la BDD
    $manager = new MongoDB\Driver\Manager('mongodb://mongodb:27017');
    $listsCollection = new MongoDB\Collection($manager, "todolist", "lists");
    $oneTask = $listsCollection->findOneAndUpdate(["_id"=>new MongoDB\BSON\ObjectId($args["idTask"])], ['$set'=>$updatesTask]);
  }else{$success=1;}

  $response->getBody()->write(json_encode(["sucess"=>$success]));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/task/{idTask}', function(Request $request, Response $response, array $args) {
  //fetch("/task/"+idTask, { method:"DELETE", headers:{"Content-Type": "application/json"} }); 
  $success=0;

  if( $args["idTask"] !== null){
    // Connexion à la BDD
    $manager = new MongoDB\Driver\Manager('mongodb://mongodb:27017');
    $listsCollection = new MongoDB\Collection($manager, "todolist", "lists");
    $oneTask = $listsCollection->findOneAndDelete(["_id"=>new MongoDB\BSON\ObjectId($args["idTask"])], ['$set'=>$updatesTask]);
  }else{$success=1;}

  $response->getBody()->write(json_encode(["sucess"=>$success]));
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
