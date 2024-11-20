<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;

use Slim\Psr7\Factory\ServerRequestFactory;

use App\App;

class AppTest extends TestCase {

  private App $app;

  public function setUp():void {
    $this->app = new App();
    $this->app->buildRoutes();
  }

  /** Pour une route on vérifie,
   * status code
   * body
   * - array, vide ou non
   * - la structure des données cad les clés obligatoires des données
  */
  #[TestDox('Testing the /about route')]
  public function testGetAbout() {
    // Create request
    $request = (new ServerRequestFactory())->createServerRequest('GET', '/about');
    // Handle the response
    $response = $this->app->handle($request);
    //Verify status code
    $this->assertEquals(200, $response->getStatusCode());
    //Problème de récupération du body : https://discourse.slimframework.com/t/unable-to-obtain-response-body-in-phpunit-for-slim-4/5049
    $jsonResponse = json_decode($response->getBody()->__toString(), true);
    $this->assertIsArray($jsonResponse);
    $this->assertNotEmpty($jsonResponse);
    // Verify keys of contents
    foreach (["version", "name"] as $key ) {
      $this->assertArrayHasKey($key, $jsonResponse);
    }
  }
    
  #[IgnoreDeprecations]
  public function testGetLists() {
    $this->assertTrue(true);

    // Create request
    $request = (new ServerRequestFactory())->createServerRequest('GET', '/lists');
    // Handle the response
    $response = $this->app->handle($request);
    //Verify status code
    $this->assertEquals(200, $response->getStatusCode());
    //Problème de récupération du body : https://discourse.slimframework.com/t/unable-to-obtain-response-body-in-phpunit-for-slim-4/5049
    $jsonResponse = json_decode($response->getBody()->__toString(), true);
    $this->assertIsArray($jsonResponse);
    $this->assertNotEmpty($jsonResponse);
    // Verify keys of contents
    foreach ($jsonResponse as $oneList) {
      foreach (["id", "title", "description", "date", "done"] as $key ) {
        $this->assertArrayHasKey($key, $oneList);
      }
    }
  }

  /**
   * Tester différents entrées et les réponses possibles
   */
  public function testPostTask():string {
    // Create request
    $request = (new ServerRequestFactory())->createServerRequest('POST', '/task');
    $request = $request->withHeader("Content-Type", "application/json");
    $request->getBody()->write(json_encode(["title"=>"testTitle", "description"=>"Test Description"]));
    // Handle the response
    $response = $this->app->handle($request);
    //Verify status code
    $this->assertEquals(201, $response->getStatusCode());
    //Problème de récupération du body : https://discourse.slimframework.com/t/unable-to-obtain-response-body-in-phpunit-for-slim-4/5049
    $jsonResponse = json_decode($response->getBody()->__toString(), true);
    $this->assertIsArray($jsonResponse);
    $this->assertNotEmpty($jsonResponse);
    // Verify keys of contents
    foreach (["success", "idTask"] as $key ) {
      $this->assertArrayHasKey($key, $jsonResponse);
    }
    return $jsonResponse["idTask"]??"";
  }

  #[Depends('testPostTask')]
  #[IgnoreDeprecations]
  public function testGetTask(string $idTask) {
    // Create request
    $request = (new ServerRequestFactory())->createServerRequest('GET', '/task/'.$idTask);
    // Handle the response
    $response = $this->app->handle($request);
    //Verify status code
    $this->assertEquals(200, $response->getStatusCode());
    //Problème de récupération du body : https://discourse.slimframework.com/t/unable-to-obtain-response-body-in-phpunit-for-slim-4/5049
    $jsonResponse = json_decode($response->getBody()->__toString(), true);
    $this->assertIsArray($jsonResponse);
    $this->assertNotEmpty($jsonResponse);
    // Verify keys of contents
    foreach (["id", "title", "description", "date", "done"] as $key ) {
      $this->assertArrayHasKey($key, $jsonResponse);
    }
  }

  #[Depends('testPostTask')]
  public function testPutTask(string $idTask) {
    // Create request
    $request = (new ServerRequestFactory())->createServerRequest('PUT', '/task/'.$idTask);
    $request = $request->withHeader("Content-Type", "application/json");
    $request->getBody()->write(json_encode(["description"=>"Modifying Description"]));
    // Handle the response
    $response = $this->app->handle($request);
    //Verify status code
    $this->assertEquals(200, $response->getStatusCode());
    //Problème de récupération du body : https://discourse.slimframework.com/t/unable-to-obtain-response-body-in-phpunit-for-slim-4/5049
    $jsonResponse = json_decode($response->getBody()->__toString(), true);
    $this->assertIsArray($jsonResponse);
    $this->assertNotEmpty($jsonResponse);
    // Verify keys of contents
    foreach (["success"] as $key ) {
      $this->assertArrayHasKey($key, $jsonResponse);
    }
  }

  #[Depends('testPostTask')]
  public function testDeleteTask(string $idTask) {
    // Create request
    $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/task/'.$idTask);
    // Handle the response
    $response = $this->app->handle($request);
    //Verify status code
    $this->assertEquals(200, $response->getStatusCode());
    //Problème de récupération du body : https://discourse.slimframework.com/t/unable-to-obtain-response-body-in-phpunit-for-slim-4/5049
    $jsonResponse = json_decode($response->getBody()->__toString(), true);
    $this->assertIsArray($jsonResponse);
    $this->assertNotEmpty($jsonResponse);
    // Verify keys of contents
      foreach (["success"] as $key ) {
        $this->assertArrayHasKey($key, $jsonResponse);
    }
  }

  public function testTodoGet() {
    $this->assertTrue(true);
  } 
}