<?php

namespace App;

use Slim\Factory\AppFactory;
use Slim\App as SlimApp;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use App\Action\ParametersHandler;
use App\Action\TaskDataHandler;
use App\Action\ResponseHandler;
use App\Action\DatabaseHandler;

use App\Model\Collection;

class App {
  /** Stores an instance of the Slim application.
  */
  private SlimApp $app;
  
  function __construct(){
    $this->app = AppFactory::create();
    // $this->app->addErrorMiddleware(false, false, false);
  }

  public function getApp():SlimApp{ return $this->app;}
  public function handle(Request $request):Response {return $this->app->handle($request);}

  public function buildRoutes():static{
    $this->app->get('/', function (Request $request, Response $response, array $args) {
      return (new ResponseHandler())->createResponse($response, "Hello world!", 200, "text/html; charset=UTF-8");
    });
      
    $this->app->get('/about', function (Request $request, Response $response, array $args) {
      return (new ResponseHandler())->createResponse(
        $response, ["name"=>"test-slim", "version"=>"0.2.0"], 200, "application/json; charset=UTF-8"
      );
    });
    
    $this->app->get('/lists', function (Request $request, Response $response, array $args) {
      // Récupération des données
      $taskArray = (new DatabaseHandler("todolist"))->read([], "lists");
      $taskArray = (new Collection("lists"))->read([]); // OU $taskArray = (new Collection("lists"))->getMany([]);
      // $taskArray = (new Task())->read([]); OU $taskArray = (new Task())->getMany([]);
      return (new ResponseHandler())->createResponse($response, $taskArray, 200, "application/json");
    });
    
    $this->app->get('/task/{idTask}', function(Request $request, Response $response, array $args) {
      //fetch("/task/"+idTask, { method:"GET", headers:{"Content-Type": "application/json"}} ); 
      $success=0;
      $parameters = (new ParametersHandler())->getParameters($request->getBody(), $args);
      $oneTask=[];
      $dataHandler = new TaskDataHandler();
      if( isset($parameters["idTask"]) ){
        // Récupération des données
        // $oneTask = (new DatabaseHandler("todolist"))->readById($parameters["idTask"], "lists");
        $oneTask = (new Collection("lists"))->readByID($parameters["idTask"])[0];
      }
      return (new ResponseHandler())->createResponse($response, $oneTask, 200, "application/json");
    });
    
    $this->app->post('/task', function(Request $request, Response $response, array $args) {
      //fetch("/task", { method:"POST", headers:{"Content-Type": "application/json"}, body:JSON.stringify({"title":"Faire le tuto Svelte", "description":"Suivre tout le tutoriel pour se former à Slim"})} ); 
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
      return (new ResponseHandler())->createResponse($response, ["success"=>$success, "idTask"=>$idTask], $status, "application/json");
    });
    
    $this->app->put('/task/{idTask}', function(Request $request, Response $response, array $args) {
      //fetch("/task/"+idTask, { method:"PUT", headers:{"Content-Type": "application/json"}, body:JSON.stringify({"description":"Nouvelle description"})} ); 
      $success=0;
      //Récupération des données en body
      $parameters = (new ParametersHandler())->getParameters($request->getBody(), $args);
      $dataHandler = new TaskDataHandler();
      if( $dataHandler->verifyData($parameters) &&  isset($parameters["idTask"])){
        $updatesTask = $dataHandler->getFormattedData($parameters);
        if( $updatesTask != []){
          // Modification des données
          $oneTask = (new DatabaseHandler("todolist"))->update($parameters["idTask"], $updatesTask, "lists");
        }else{$success=1;}
      }else{$success=2;}
    
      return (new ResponseHandler())->createResponse($response, ["success"=>$success], 200, "application/json");
    });
    
    $this->app->delete('/task/{idTask}', function(Request $request, Response $response, array $args) {
      //fetch("/task/"+idTask, { method:"DELETE", headers:{"Content-Type": "application/json"} }); 
      $success=0;
      $parameters = (new ParametersHandler())->getParameters($request->getBody(), $args);
      if( isset($parameters["idTask"]) ){
        // Suppression des données
        $oneTask = (new DatabaseHandler("todolist"))->delete($parameters["idTask"], "lists");
      }else{$success=1;}
    
      return (new ResponseHandler())->createResponse($response, ["success"=>$success], 200, "application/json");
    });

    return $this;
  }
}