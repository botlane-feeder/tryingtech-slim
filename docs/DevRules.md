# DevRules

Les règles de développement pour assurer un développement universelle et facile à lire

## BDD - MongoDB

On utilise une classe surcouche des librairies PHP ou MongoDB.
- Dans le cas d'une simple et rapide appel à la BDD, on utilise la classe `Collection` qui possède les méthodes pour manipuler la BDD en CRUD.
  - Le cas général est qu'une requête va récupérer un document ou une partie et va modifie un document ou une partie
  - Dans le cas d'une requête qui a besoin de plusieurs documents et va en modifier plusieurs, on fera une classe qui fera cette abstraction et la manipulation des différents document, n'utilisant à chaque fois qu'un 
- Dans le cas d'objet qui vont être manipulé pendant toute la prcocédure, on utilisera une copie locale des données pour enregristrer en local toutes les modifications et à la fin, si tout s'est bien passé, on enregistre avec un `save()` (une sorte d'ODM)

Objets à créer pour avoir une interface confortable entre l'application et la base de données
- Create a Database Connection Class: Create a class that handles the connection to the MongoDB database. This class should have methods for connecting to the database, executing queries, and handling errors.
- Create a Data Access Object (DAO) Class for Each Collection: For each collection in your MongoDB database, create a DAO class that handles the data access for that collection. This class should have methods for creating, reading, updating, and deleting documents in the collection.
- Use Dependency Injection: Use dependency injection to pass the MongoDB connection object to the DAO classes. This will make your code more modular and testable.
- Use Interfaces: Use interfaces to define the methods that each DAO class should have. This will make it easier to swap out different implementations of the DAO classes without affecting the rest of your code.
- Use a Query Builder or ORM: Consider using a query builder or an ORM (Object-Document Mapping) library to simplify the process of executing queries and mapping the results to objects.


## Tests API

Ils y a deux types de tests pour les API : 
- les tests unitaires des routes
- les tests de performance

Pour réaliser les tests unitaires, il faut que l'application soit disponible dans une classe, afin que l'outil pour faire les tests puissent instancier cette objet et créer une fausse requête afin de contrôler la response.
L'objectif des ces tests est de faire les tours des possibilités afin de s'assurer que le retour est bien maitrisé.
Ces tests doivent être automatisés afin de pouvoir faire tourner ces tests aussi régulièrement que possible pour s'arrurer que les fonctionnalités sont toujours correctes.

Pour réaliser les tests de performance, il faut que l'application soit déployée sur une prod avec des performances connues, puis faire tourner un scénario classique mais réaliste afin de déceler une faiblesse dans ce nouveau code.