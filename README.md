# Tryingtech - Slim

Ce projet est un test de la technologie Slim pour créer des applications web.  
Durant ce document, on découvrira la technolohie Slim pour comprendre comment elle fonctionne, comment l'utiliser.


Slim est un framework qui a pris tous les principes PSR, c'est à dire les bonnes pratiques web pour faire une application web avec PHP, et a créé un framework très simple permettant de créer une application web en respectant les [PSR](https://www.php-fig.org/).  
Slim propose des outils internes pour y répondre mais permet également d'utiliser des outils tiers.
Son but est de créer simplement une application web en PHP en respectant les PSR.

**WIP** : Réprendre à [Request](https://www.slimframework.com/docs/v4/objects/request.html)

Lire et prendre en exemple : [HiddenHat - A PHP Web app in twenty minutes with Slim](https://hiddenhat.press/php-web-app-fast-with-slim/)

## Lancement du projet

Télécharger le projet et son code source.  
Installer les dépendances composer avec `composer install`. (Avoir bien installer composer sur sa machine au préalable.)  
Lancer l'application en conteneur avec `docker compose up`. (On peut ajouter l'option `-d` pour qu'il tourne en fond.)  

## Comprendre Slim

### Description SLIM

Slim est un micro framework PHP permettant de créer rapidement une application web puissante et des APIs.  
La simplicité de Slim est qu'il s'occupe de router des requêtes HTTP, en appelant des fonctions montées et en retournant des réponses HTTP. Et c'est tout.

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

### Micro-API

Après avoir installer le minimum, on peut créer une API minimum

```php
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

## Architecture d'une API

### Dossiers

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

1. Instanciation : durant l'instanciation, Slim appelle les services par defaut des dépendances de l'application
2. Routing : l'objet routeur de l'application monte une route pour chaque fonction appelée : `get()`, `post()`, `put()` ...
3. Lancement : l'application tourne avec la methode `run()`
3. a. Middleware : défini des couches concentriques où le centre est l'application Slim, les couches appellent des fonctions englobant le traitement
3. b. Runner : une fois dans la couche de l'application, la requête HTTP est envoyée à la fonction de la route appropriée, sinon jeté une exception
3. c. Depilage middleware : Sortie des Middleware : une fois la couche application traitée, les middlewares sont appelés en sortie, un à un
3. d. Réponse HTTP : Préparation de la réponse HTTP

### Formatage des données - PSR-7

The PSR-7 interface provides these methods to transform Request and Response objects:

- withProtocolVersion($version)
- withHeader($name, $value)
- withAddedHeader($name, $value)
- withoutHeader($name)
- withBody(StreamInterface $body)
The PSR-7 interface provides these methods to transform Request objects:

- withMethod($method)
- withUri(UriInterface $uri, $preserveHost = false)
- withCookieParams(array $cookies)
- withQueryParams(array $query)
- withUploadedFiles(array $uploadedFiles)
- withParsedBody($data)
- withAttribute($name, $value)
- withoutAttribute($name)

The PSR-7 interface provides these methods to transform Response objects:
- withStatus($code, $reasonPhrase = '')


### Middleware

Une application web est un traitement et une manipulation d'une requête et d'une réponse.  
Mais si on a besoin de travailler avant et après cette manipulation, on fait appel à un middleware.
Par exemple pour protégere son application, pour authentifier une requête, pour avoir des logs

Les middleware sont représentées comme des couches autour de l'application, c'est à dire le traintement d'une requête et d'une réponse.  
Et on peut faire un pré-traitement ou un post-traitement, ou une redirection, avant d'arriver à la couche de l'application.

![middleware.png](docs/assets/middleware.png)

Les middlewares sont gérées d'après la documentation [PSR-15](https://www.php-fig.org/psr/psr-15/)

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

### Dependency Container

