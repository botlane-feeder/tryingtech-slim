<?php

namespace App\Model;

use MongoDB\Driver\Manager;

class Collection{
  function __construct($collectionName){
    $this->config = [
      "dbName"=>"todolist",
      "collectionName"=>$collectionName,
      "hostname"=>"mongodb",
      "port"=>27017
    ];
    $this->collection = new \MongoDB\Collection($this->getManager(), $this->getConfig()["dbName"], $collectionName);
  }
  protected function getConfig():array{ return $this->config; }

  protected function getManager():Manager{
    return new Manager("mongodb://".$this->getConfig()["hostname"].":".$this->getConfig()["port"]);
  }


  public function get(mixed $filter=[]):array{
    $data = [];

    return $data;
  }
  public function readByID(string $id):array{
    return $this->read( ["_id" => new \MongoDB\BSON\ObjectId($id)] );
  }
  public function read(array $filter):array{
    $query = new \MongoDB\Driver\Query($filter);
    $cursor = $this->collection->find($filter);
    $data = json_decode(json_encode($cursor->toArray()), true);
    // Lint l'id de chaque tâche
    foreach ($data as $key => $oneTask) {
      $oneTask["id"]=$oneTask["_id"]['$oid'];
      unset($oneTask["_id"]);
      $data[$key] = $oneTask;
    }

    return $data;
  }

  public function create(array $data):string{
    $this->collection->insertOne($task);
    return $id;
  }

  public function update(mixed $filter, array $data, string $collectionName):static{
    // Vérification si le paramètres filtre et un OID, alors transformation en filtre
    $this->collection->findOneAndUpdate($filter, ['$set'=>$updatesTask]);
    return $this;
  }

  public function delete(string $id, string $collectionName):static{
    // Vérification si le paramètres filtre et un OID, alors transformation en filtre
    $filter = ["_id" => new \MongoDB\BSON\ObjectId($id)];
    $this->collection->findOneAndDelete($filter);
    return $this;
  }

}