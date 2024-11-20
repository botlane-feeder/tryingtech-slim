<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;

use App\Action\ParametersHandler;
use App\Action\TaskDataHandler;
use App\Action\ResponseHandler;
use App\Action\DatabaseHandler;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// $app->addErrorMiddleware(false, true, true);

/** Emplacement pour les middlewares
 * la définition des middlewares s'ajoute dans un pile : la première function Middleware sera executées en dernier
 */

/** Routes de l'application
 */

$app->get('/', function (Request $request, Response $response, array $args) {
  return (new ResponseHandler())->createResponse($response, "Hello world!", 200, "text/html; charset=UTF-8");
});
  
$app->get('/about', function (Request $request, Response $response, array $args) {
  return (new ResponseHandler())->createResponse(
    $response, ["name"=>"test-slim", "version"=>"0.1.0"], 200, "application/json; charset=UTF-8"
  );
});

$app->get('/lists', function (Request $request, Response $response, array $args) {
  // Récupération des données
  $taskArray = (new DatabaseHandler("todolist"))->read([], "lists");
  return (new ResponseHandler())->createResponse($response, $taskArray, 200, "application/json");
});

$app->get('/task/{idTask}', function(Request $request, Response $response, array $args) {
  $success=0;
  $parameters = (new ParametersHandler())->getParameters($request->getBody(), $args);
  $oneTask=[];
  $dataHandler = new TaskDataHandler();
  if( isset($parameters["idTask"]) ){
    // Récupération des données
    $oneTask = (new DatabaseHandler("todolist"))->readById($parameters["idTask"], "lists");
  }
  return (new ResponseHandler())->createResponse($response, $oneTask, 200, "application/json");
});

$app->post('/task', function(Request $request, Response $response, array $args) {
  $success=0;
  $status=201;
  //Récupération des données en body
  $parameters = (new ParametersHandler())->getParameters($request->getBody(), $args);
  // Vérification des bonnes données avant écriture
  $dataHandler = new TaskDataHandler();
  if( $dataHandler->verifyData($parameters, true) ){
    $task = $dataHandler->getFormattedData($parameters, true);
    // Écriture en BDD
    $idTask = (new DatabaseHandler("todolist"))->create($task, "lists");
  }else{
    error_log("Problem in parameters");
    $success=1;
    $status=405;
  }
  
  return (new ResponseHandler())->createResponse($response, ["sucess"=>$success, "idTask"=>$idTask], $status, "application/json");
});

$app->put('/task/{idTask}', function(Request $request, Response $response, array $args) {
  $success=0;
  //Récupération des données en body
  $parameters = (new ParametersHandler())->getParameters($request->getBody(), $args);
  $dataHandler = new TaskDataHandler();
  if( $dataHandler->verifyData($parameters) &&  isset($parameters["idTask"])){
    $updatesTask = $dataHandler->getFormattedData($parameters);
    if( $updatesTask != []){
      // Modification des données
      $oneTask = (new DatabaseHandler("todolist"))->update($parameters["idTask"], $updatesTask, "lists");
      error_log("here");
    }else{$success=1;}
  }else{$success=2;}

  return (new ResponseHandler())->createResponse($response, ["sucess"=>$success], 200, "application/json");
});

$app->delete('/task/{idTask}', function(Request $request, Response $response, array $args) {
  $success=0;
  $parameters = (new ParametersHandler())->getParameters($request->getBody(), $args);
  if( isset($parameters["idTask"]) ){
    // Suppression des données
    $oneTask = (new DatabaseHandler("todolist"))->delete($parameters["idTask"], "lists");
  }else{$success=1;}

  return (new ResponseHandler())->createResponse($response, ["sucess"=>$success], 200, "application/json");
});

$app->run();