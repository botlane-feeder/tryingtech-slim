# Tryingtech - Slim

Ce projet est un test de la technologie Slim pour créer des applications web.  
Durant ce document, on découvrira la technologie Slim pour comprendre comment elle fonctionne, comment l'utiliser.


Slim est un framework qui a pris tous les principes PSR, c'est à dire les bonnes pratiques web pour faire une application web avec PHP, et a créé un framework très simple permettant de créer une application web en respectant les [PSR](https://www.php-fig.org/).  
Slim propose des outils internes pour y répondre mais permet également d'utiliser des outils tiers.
Son but est de créer simplement une application web en PHP en respectant les PSR.

**WIP** : Réprendre à [Request](https://www.slimframework.com/docs/v4/objects/request.html)

Lire et prendre en exemple : [HiddenHat - A PHP Web app in twenty minutes with Slim](https://hiddenhat.press/php-web-app-fast-with-slim/)

## Lancement du projet

Télécharger ce projet et son code source, avec `git clone`.  
Installer les dépendances composer avec `composer install`. (Avoir bien installer [composer](https://getcomposer.org/doc/00-intro.md) sur sa machine au préalable.)  
 - Si votre système n'est pas à jour par rapport aux prérequis composer `composer install --ignore-platform-reqs`
Lancer l'application en conteneur avec `docker compose up`. (On peut ajouter l'option `-d` pour qu'il tourne en fond.)  

## Comprendre Slim

### Description SLIM

Slim est un micro framework PHP permettant de créer rapidement une application web puissante et des APIs.  
La simplicité de Slim est qu'il s'occupe de router des requêtes HTTP, en appelant des fonctions montées et en retournant des réponses HTTP.  
Et c'est tout.

En vrai, Slim a beaucoup de methodes pour gérer ces requêtes, mais le principe reste aussi simple.  
Pour le reste, soit on le développe, soit on charge une librairie.  

### Installation Slim

Pour travailler sur Slim, il faut installer : 
- slim/slim : [doc](https://www.slimframework.com/docs/v4/)
- slim/psr7 : [doc](https://www.php-fig.org/psr/psr-7/)

Que l'on peut créer avec un composer comme suit : 
```json
{
  "require": {
    "slim/slim": "^4.13",
    "slim/psr7": "^1.4"
  },
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

Puis il faut télécharger les dépendances composer avec `composer install`.

### Micro-API

Après avoir installer le minimum, on peut créer une API d'une route triviale

```php
# fichier public/index.php
<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();
```

### Groupe de routes

ex : 
```php
# fichier public/index.php
<?php
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
```

## Découverte de Slim par sa doc

### Arborescence d'une API

*WIP*

L'installation des dépendances se fait dans le dossier `vendor/`.

Basé sur mon expérience, je propose cette structure d'arborescence, même si elle n'est pas obligatoire pour le bon fonctionnement :
- top level on retrouve
  - `app/` : pour les fichiers de l'application
  - `config/` : pour les configurations PHP
  - `public/` : pour le fichier index.php et surtout les fichiers accessible depuis l'exterieur
  - `routes/` : pour les ...

project/  
│  
├── app/  
│   ├── Controllers/  
│   │   └── HomeController.php  
│   │  
│   ├── Middleware/  
│   │   └── AuthenticationMiddleware.php  
│   │  
│   ├── Models/  
│   │   └── UserModel.php  
│   │  
│   └── Services/  
│       └── AuthService.php  
│  
├── config/  
│   └── settings.php  
│  
├── public/  
│   └── index.php  
│  
├── routes/  
│   ├── api.php  
│   └── web.php  
│  
├── templates/  
│   └── home.twig  
│  
├── vendor/  
│  
├── .env  
├── composer.json  
└── composer.lock  


### Les étapes traversées par chaque requête (application life circle)

Application life circle = Instanciation d'une app -> Création des routes pour le Routing -> Lancement de la fonction `run()`

1. Instanciation : durant l'instanciation, Slim appelle les services par defaut des dépendances de l'application
2. Routing : l'objet routeur de l'application monte une route pour chaque fonction appelée : `get()`, `post()`, `put()` ...
3. Lancement : l'application tourne avec la methode `run()`
3. a. Middleware : défini des couches concentriques où le centre est l'application Slim, les couches appellent des fonctions englobant le traitement
3. b. Runner : une fois dans la couche de l'application, la requête HTTP est envoyée à la fonction de la route appropriée, sinon jeté une exception
3. c. Depilage middleware : Sortie des Middleware : une fois la couche application traitée, les middlewares sont appelés en sortie, un à un
3. d. Réponse HTTP : Préparation de la réponse HTTP

### Formatage des données - PSR-7

The PSR-7 interface provides these methods to transform `Request` and `Response` objects :  
- withProtocolVersion($version)
- withHeader($name, $value)
- withAddedHeader($name, $value)
- withoutHeader($name)
- withBody(StreamInterface $body)

The PSR-7 interface provides these methods to transform `Request` objects :  
- withMethod($method)
- withUri(UriInterface $uri, $preserveHost = false)
- withCookieParams(array $cookies)
- withQueryParams(array $query)
- withUploadedFiles(array $uploadedFiles)
- withParsedBody($data)
- withAttribute($name, $value)
- withoutAttribute($name)

The PSR-7 interface provides these methods to transform `Response` objects :  
- withStatus($code, $reasonPhrase = '')


### Middleware

Une application web est un traitement et une manipulation d'une requête et d'une réponse.  
Mais si on a besoin de travailler avant et après cette manipulation, on fait appel à un middleware.
Par exemple pour protégere son application, pour authentifier une requête, pour avoir des logs

Les middleware sont représentées comme des couches autour de l'application, c'est à dire le traintement d'une requête et d'une réponse.  
Et on peut faire un pré-traitement ou un post-traitement, ou une redirection, avant d'arriver à la couche de l'application.

![middleware.png](docs/assets/middleware.png)

Les middlewares sont définis d'après la documentation [PSR-15](https://www.php-fig.org/psr/psr-15/)

Attention, un middleware n'est qu'une fonction qui sera exécuté par un appel sur une route, comme l'authentification.  
**À condition que l'on ajoute à cette route le middleware.**

#### Composition

Une fonction middleware aura un effet sur la requête envoyée et/ou la réponse.
Donc elle doit :
- être définie et ajoutée à l'application
- avoir en paramètre la `Response` et la `RequestHandler`

Son traitement peut-être de vérifier l'authentification, de gérer des logs, de gérer une connexion ou une configuration au préalable ...

#### Utilisation

Un middleware est une fonction qui sera appelée avant l'appel à la fonction de l'application demandée.
Cependant son execution et son traitement sur la `Response` ou la `RequestHandler` sera fait après l'appel de la fonction de l'application demandée.

Ainsi si on souhaite que le controleur renvoie un "string" ou un tableau de données JSON, on peut avoir un middleware pour retravailler la réponse.

Ex : 
```php
# fichier public/index.php

<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

//Instanciation de l'application
$app = AppFactory::create();

//Création de fonctions middleware
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

//Création de la ou des routes
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
})->add($beforeMV)->add($afterMV);

//Lancement de l'application
$app->run();
```

Va afficher en console :
```bash
NOTICE: PHP message: 3
NOTICE: PHP message: 1
NOTICE: PHP message: Hello world!
```

Va renvoyer en `Response` :
```html
<body>BEFORE Hello world! AFTER</body>
```


### Dependency Container

Slim vient avec son conteneur de dépendance, qui permet d'injecter des dépenses dans toutes les routes de l'application et de les utiliser comme simple méthode.

ex : 
```php
#fichier public/index.php
<?php

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Création du conteneur de dépendances
$container = new Container();
// Ajout d'une dépendances, qui à l'appelle executera le fonction
$container->set('myService', function () {
    $settings = [...];
    return new MyService($settings);
});
// Création d'une route, à l'execution de cette route, la fonction récupèrera la dépendance 'myService'
$app->get('/foo', function (Request $request, Response $response, $args) {
    if ($this->has('myService')) {
        $myService = $this->get('myService');
        // Doing Someting with $myService ;
    }
    return $response;
});

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();
```

En option, on peut aussi instancier l'application directement avec le conteneur de dépendances
```php
$app = AppFactory::createFromContainer($container);
```

### Request

Chaque route instancie une `Request` et on utilise ses méthodes pour nos besoins.

Par exemple, récupérer le body :  
```php
$postParameters = json_decode($request->getBody(), true);
```

Ou encore, récupérer les arguments variables

### Responses



## BDD MongoDB

Pour rendre plus intéressant une application web, il faut des données disponible que l'on va mettre à jour.  
Pour cela, on fait appel à une base de données !

Pour plus de faciliter et afin d'utiliser des technologies actuelles, nous utiliserons MongoDB pour notre application.

On peut utiliser la solution Cloud de MongoDB à savoir Atlas, ou bien on peut utiliser une base données locale.

### Modification du docker-compose

Ajout d'un conteneur pour la base de données, à partir d'une image `mongodb/mongodb-community-server`

```yml
  mongodb:
    image: mongodb/mongodb-community-server
    networks:
      - app-network
```


### Présentation des utilitaires

Au préalable, il faut installer le driver MongoDB avec PECL.
Et configurer mongodb dans le fichier de configuration de `php.ini`

Ces deux étapes sont faites automatiquement dans le Dockerfile PHP-FPM

Après cette installation, nous avons à disposition le [driver PHP](https://www.php.net/manual/en/book.mongodb.php) et ces utilitaires :
- `MongoDB\Driver\Manager` : le manager qui ouvre une connexion avec la SGBD mongoDB
- `MongoDB\Driver\Query` : un utilitaire Query pour faire des requêtes afin de récupérer des données
- `MongoDB\Driver\BulkWrite` : un utilitaire Bulk pour faire des requêtes insert, update, ou delete

MongoDB nous propose également sa propre librairie que l'on peut installer avec composer : `composer require mongodb/mongodb`
- `MongoDB\Client` : un [utilitaire](https://www.mongodb.com/docs/php-library/current/reference/class/MongoDBClient/) pour se connecter à la SGBD mongoDB
- `MongoDB\Database` : un [utilitaire](https://www.mongodb.com/docs/php-library/current/reference/class/MongoDBDatabase/) pour interroger une base de données
- `MongoDB\Collection` : un [utilitaire](https://www.mongodb.com/docs/php-library/current/reference/class/MongoDBCollection/) pour faire des requêtes dans une collection