<?php

namespace App\Action;

use MongoDB\Driver\Manager;

class DatabaseHandler{

  private array $config;

  function __construct(string $dbName){
    $this->config = [
      "dbName"=>$dbName,
      "hostname"=>"mongodb",
      "port"=>27017
    ];
  }
  private function getConfig():array{ return $this->config; }

  private function getManager():Manager{
    return new Manager("mongodb://".$this->getConfig()["hostname"].":".$this->getConfig()["port"]);
  }

  public function readByID(string $id, string $collectionName):array{
    return $this->read( ["_id" => new \MongoDB\BSON\ObjectId($id)], $collectionName );
  }
  public function read(array $filter, string $collectionName):array{
    $query = new \MongoDB\Driver\Query($filter);
    $cursor = $this->getManager()->executeQuery($this->getConfig()["dbName"].".".$collectionName, $query);
    $data = json_decode(json_encode($cursor->toArray()), true);
    // Lint l'id de chaque tâche
    foreach ($data as $key => $oneTask) {
      $oneTask["id"]=$oneTask["_id"]['$oid'];
      unset($oneTask["_id"]);
      $data[$key] = $oneTask;
    }

    return $data;
  }

  public function create(array $data, string $collectionName):string{
    $bulk = new \MongoDB\Driver\BulkWrite();
    $id = $bulk->insert($data);
    $this->getManager()->executeBulkWrite($this->getConfig()["dbName"].".".$collectionName, $bulk);
    return $id;
  }

  public function update(mixed $filter, array $data, string $collectionName):static{
    // Vérification si le paramètres filtre et un OID, alors transformation en filtre
    $filter=gettype($filter)==="array"?$filter:["_id" => new \MongoDB\BSON\ObjectId($filter)];
    $bulk = new \MongoDB\Driver\BulkWrite();
    $id = $bulk->update($filter, ['$set'=>$data]);
    $this->getManager()->executeBulkWrite($this->getConfig()["dbName"].".".$collectionName, $bulk);
    return $this;
  }

  public function delete(string $id, string $collectionName):static{
    // Vérification si le paramètres filtre et un OID, alors transformation en filtre
    $filter = ["_id" => new \MongoDB\BSON\ObjectId($id)];
    $bulk = new \MongoDB\Driver\BulkWrite();
    $id = $bulk->delete($filter);
    $this->getManager()->executeBulkWrite($this->getConfig()["dbName"].".".$collectionName, $bulk);
    return $this;
  }
}