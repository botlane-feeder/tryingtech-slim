<?php

namespace Test;

use PHPUnit\Framework\TestCase;

use Slim\Psr7\Factory\ServerRequestFactory;

use App\App;

class AppTests extends TestCase {

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

  public function testGetLists() {
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

  public function testPostTask() {
    // Create request
    $request = (new ServerRequestFactory())->createServerRequest('POST', '/task');
    $request = $request->withHeader("Content-Type", "application/json");
    $request->getBody()->write(json_encode(["title"=>"testTitle", "description"=>"Test Description"]));
    // ->withBody();
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
  }

  public function testTodoGet() {
    $this->assertTrue(true);
  } 
}