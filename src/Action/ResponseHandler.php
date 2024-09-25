<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;


class ResponseHandler{

  public function createResponse(Response $response, mixed $body, int $status=200, string $contentType="text/html"):Response{
    if(gettype($body) ==="array" ){
      $body = json_encode($body);
      if($contentType ==="text/html") $contentType = "application/json";
    }
    $response->getBody()->write($body);
  return $response->withStatus($status)->withHeader('Content-Type', $contentType);
  }

}