<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(false, true, true);

/** Routes de l'application
 * `/about` : renvoie des données de l'application comme son nom et sa version
 */

// Lancement de l'application
$app->run();

