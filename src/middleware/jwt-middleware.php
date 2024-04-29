<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;

$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);

    // Vérifiez le JWT ici

    return $response;
});
