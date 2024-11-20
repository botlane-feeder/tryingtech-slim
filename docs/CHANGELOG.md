# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),  
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]


## [0.2.0] - 2024-01-05

**Mise en place de tests unitaires dans l'application**

### REFACTOR

- Mise en place d'une classe App, qui contient la création de l'application web

### ADD

- Mise en place de tests unitaires avec PHPUnit
- Mise en place d'un Makefile pour lancer plus facilement des commandes


## [0.1.0] - 2024-01-03

**Réfactoring de l'architecture de l'application.**

### REFACTOR

- Modification de l'architecture et création de 4 classes pour les routes
  - Ajout de la classe ParametersHandler
    - Méthode publique : `getParameters()` qui retourne le tableau des paramètres tous confondus (body et args)
  - Ajout de la classe TaskDataHandler
    - Méthode publique : `verifyData()` : qui vérifie les données passés en paramètres et retourne si elles sont OK après vérification avec le Template
    - Méthode publique : `getFormattedData()` : qui renvoie un tableau des données remplis, selon le Template complet mais pas plus de données
  - Ajout de la classe ResponseHandler
    - Méthode publique : `createResponse()` qui retourne une response, construite avec les paramètres
  - Ajout de la classe DatabaseHandler
    - Méthode publique : `read()` qui récupère en BDD un ou plusieurs documents par un filtre
    - Méthode publique : `readByID()` qui récupère en BDD un document par son ID
    - Méthode publique : `create()` qui enregistre en BDD un document et retourne l'id
    - Méthode publique : `update()` qui modifie en BDD un ou plusieurs document(s)
    - Méthode publique : `delete()` qui supprime en BDD un document par son ID


## [0.0.0] - 2024-01-02

**Création de l'application et de ses 5 routes.**

### ADD

- route `GET:/about`, qui renvoie en JSON le nom et la version du projet
- route `POST:/task`, qui renvoie en JSON toutes les entrées en BDD de la collection `lists`
- route `GET:/task/{idTask}`, qui renvoie en JSON une tache si elle est trouvée dans la collection `lists`
- route `UPDATE:/task/{idTask}`, qui renvoie en JSON une tache si elle est trouvée dans la collection `lists`
- route `DELETE:/task/{idTask}`, qui renvoie en JSON une tache si elle est trouvée dans la collection `lists`